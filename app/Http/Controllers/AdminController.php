<?php

namespace App\Http\Controllers;

use App\Models\AiCall;
use App\Models\Business;
use App\Models\QuotaRule;
use App\Models\Receipt;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Services\ReceiptPipeline;
use App\Support\Phone;
use App\Support\Text;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('cari', ''));

        return view('admin.index', [
            'stats' => [
                'businesses' => Business::query()->where('is_demo', false)->count(),
                'demo_businesses' => Business::query()->where('is_demo', true)->count(),
                'receipts' => Receipt::query()->count(),
                'suppliers' => Supplier::query()->whereNull('owner_business_id')->count(),
            ],
            'aiCalls' => AiCall::query()
                ->where('created_at', '>=', now()->subDays(7))
                ->selectRaw('purpose, status, count(*) as total, avg(latency_ms) as latency')
                ->groupBy('purpose', 'status')
                ->orderBy('purpose')
                ->get(),
            'suppliers' => Supplier::query()
                ->whereNull('owner_business_id')
                ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.str_replace(['%', '_'], ['\%', '\_'], $search).'%'))
                ->withCount('products')
                ->orderByRaw("case when verification = 'pernyataan' then 0 else 1 end")
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'quotas' => QuotaRule::query()->get()->keyBy('group'),
            'groups' => config('pasok.quota_groups'),
            'search' => $search,
            'products' => Product::query()->orderBy('name')->get(['id', 'name', 'base_unit']),
            'regencies' => config('pasok.regencies'),
        ]);
    }

    public function verify(Request $request, Supplier $supplier, ReceiptPipeline $pipeline): RedirectResponse
    {
        abort_if($supplier->owner_business_id !== null, 404);

        $data = $request->validate(['verification' => ['required', Rule::in([Supplier::VERIFIED, Supplier::DECLARED])]]);

        $supplier->update(['verification' => $data['verification']]);
        $pipeline->refreshSupplierOrigins($supplier);

        return back()->with('status', "Status {$supplier->name} diubah menjadi {$data['verification']}.");
    }

    public function storeSupplier(Request $request, ReceiptPipeline $pipeline): RedirectResponse
    {
        if (filled($request->input('phone')) && ! Phone::normalize($request->input('phone'))) {
            return back()->withInput()->withErrors(['phone' => 'Nomor WA tidak valid. Contoh: 0812 3456 7890.']);
        }

        $request->merge(['phone' => Phone::normalize($request->input('phone'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40', Rule::unique('suppliers', 'phone')],
            'regency' => ['required', Rule::in(config('pasok.regencies'))],
            'verification' => ['required', Rule::in([Supplier::VERIFIED, Supplier::DECLARED])],
            'notes' => ['nullable', 'string', 'max:255'],
            'products' => ['required', 'array', 'max:5'],
            'products.*.product_id' => ['nullable', 'integer', Rule::exists('products', 'id')],
            'products.*.origin' => ['nullable', Rule::in([SupplierProduct::BALI, SupplierProduct::OUTSIDE])],
            'products.*.capacity' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'products.*.unit' => ['nullable', 'string', 'max:20'],
            'products.*.price' => ['nullable', 'integer', 'min:0', 'max:100000000'],
        ], [
            'phone.unique' => 'Nomor WA ini sudah terdaftar sebagai pemasok.',
        ]);

        $rows = collect($data['products'])->filter(fn ($row) => filled($row['product_id'] ?? null))->unique('product_id');

        if ($rows->isEmpty()) {
            return back()->withInput()->withErrors(['products' => 'Isi minimal satu produk.']);
        }

        $units = Product::query()->whereIn('id', $rows->pluck('product_id'))->pluck('base_unit', 'id');

        $supplier = DB::transaction(function () use ($data, $rows, $units) {
            $supplier = Supplier::query()->create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'regency' => $data['regency'],
                'verification' => $data['verification'],
                'notes' => $data['notes'] ?? null,
                'is_demo' => false,
                'active' => true,
            ]);

            foreach ($rows as $row) {
                $unit = filled($row['unit'] ?? null) ? Text::unit($row['unit']) : $units[$row['product_id']];

                $supplier->products()->create([
                    'product_id' => (int) $row['product_id'],
                    'origin' => $row['origin'] ?? SupplierProduct::BALI,
                    'capacity_per_week' => filled($row['capacity'] ?? null) ? (float) $row['capacity'] : null,
                    'capacity_unit' => $unit,
                    'price_per_unit' => filled($row['price'] ?? null) ? (int) $row['price'] : null,
                    'price_unit' => $unit,
                ]);
            }

            return $supplier;
        });

        $pipeline->refreshSupplierOrigins($supplier);

        return redirect()->route('admin.index')->with('status', "{$supplier->name} ditambahkan sebagai pemasok.");
    }

    public function quotas(Request $request): RedirectResponse
    {
        $groups = array_keys(config('pasok.quota_groups'));

        $data = $request->validate([
            'quota' => ['required', 'array'],
            'quota.*' => ['required', 'integer', 'min:0', 'max:100'],
            'source' => ['required', 'string', 'max:255'],
        ]);

        foreach ($groups as $group) {
            if (isset($data['quota'][$group])) {
                QuotaRule::query()->updateOrCreate(['group' => $group], ['min_percent' => (int) $data['quota'][$group], 'source' => $data['source']]);
            }
        }

        Cache::forget('pasok:kuota');

        return back()->with('status', 'Kuota diperbarui.');
    }
}
