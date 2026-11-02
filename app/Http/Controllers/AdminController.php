<?php

namespace App\Http\Controllers;

use App\Models\AiCall;
use App\Models\Business;
use App\Models\QuotaRule;
use App\Models\Receipt;
use App\Models\Supplier;
use App\Services\ReceiptPipeline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
