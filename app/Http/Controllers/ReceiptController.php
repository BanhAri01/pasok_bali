<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessReceipt;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Supplier;
use App\Services\ComplianceCalculator;
use App\Services\ProductCatalog;
use App\Services\ReceiptPipeline;
use App\Support\Text;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceiptController extends Controller
{
    public const SAMPLE_DIR = 'data/nota-demo';

    public function index(Request $request): View
    {
        $status = $request->query('status');

        return view('app.receipts.index', [
            'receipts' => Receipt::query()
                ->with('supplier:id,name')
                ->withCount(['items', 'items as review_count' => fn ($query) => $query->where('needs_review', true)])
                ->when(in_array($status, [Receipt::PROCESSING, Receipt::NEEDS_CHECK, Receipt::APPROVED], true), fn ($query) => $query->where('status', $status))
                ->latest('receipt_date')
                ->latest('id')
                ->paginate(15)
                ->withQueryString(),
            'status' => $status,
        ]);
    }

    public function create(Request $request): View
    {
        $registered = Supplier::query()
            ->visibleTo($request->user()->business_id)
            ->where('active', true)
            ->pluck('name')
            ->mapWithKeys(fn ($name) => [Text::key($name) => $name]);

        $key = database_path(self::SAMPLE_DIR.'/kunci.json');

        $suppliers = collect(is_file($key) ? (json_decode((string) file_get_contents($key), true) ?: []) : [])
            ->filter(fn ($row) => is_array($row) && isset($row['file']))
            ->mapWithKeys(fn ($row) => [$row['file'] => (string) ($row['supplier_name'] ?? '')]);

        $samples = collect(self::samples())
            ->map(fn ($file) => [
                'file' => $file,
                'supplier' => $registered->get(Text::key($suppliers->get($file))),
            ])
            ->sortBy(fn ($sample, $index) => [$sample['supplier'] === null, $index])
            ->values()
            ->all();

        return view('app.receipts.create', ['samples' => $samples]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'foto' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:'.config('pasok.receipt.max_kb')],
        ], [
            'foto.required' => 'Pilih atau ambil foto nota terlebih dahulu.',
            'foto.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 8 MB.',
        ]);

        $this->throttle($request);

        $business = $request->attributes->get('business');
        $file = $request->file('foto');
        $path = $file->storeAs('nota/'.$business->id, Str::uuid().'.'.$file->extension(), 'local');

        return $this->queue($request, $path);
    }

    public function storeSample(Request $request, string $sample): RedirectResponse
    {
        $source = self::samplePath($sample);
        abort_unless($source, 404);

        $this->throttle($request);

        $business = $request->attributes->get('business');
        $path = 'nota/'.$business->id.'/'.Str::uuid().'.'.pathinfo($source, PATHINFO_EXTENSION);
        Storage::disk('local')->put($path, file_get_contents($source));

        return $this->queue($request, $path);
    }

    public function sampleImage(string $sample): BinaryFileResponse
    {
        $source = self::samplePath($sample);
        abort_unless($source, 404);

        return response()->file($source, ['Cache-Control' => 'private, max-age=3600']);
    }

    public function show(Receipt $receipt, ProductCatalog $catalog): View
    {
        $receipt->load(['items' => fn ($query) => $query->orderBy('id'), 'supplier']);

        return view('app.receipts.show', [
            'receipt' => $receipt,
            'products' => $catalog->all()->map(fn ($product) => ['id' => $product->id, 'name' => $product->name, 'category' => $product->category, 'unit' => $product->base_unit])->values(),
            'suppliers' => Supplier::query()->visibleTo($receipt->business_id)->where('active', true)->orderBy('name')->get(['id', 'name', 'regency', 'verification']),
            'categories' => config('pasok.categories'),
        ]);
    }

    public function status(Receipt $receipt): JsonResponse
    {
        return response()->json(['status' => $receipt->status]);
    }

    public function image(Receipt $receipt): StreamedResponse
    {
        abort_unless($receipt->image_path && Storage::disk('local')->exists($receipt->image_path), 404);

        return Storage::disk('local')->response($receipt->image_path, null, ['Cache-Control' => 'private, max-age=600']);
    }

    public function update(Request $request, Receipt $receipt, ReceiptPipeline $pipeline, ComplianceCalculator $compliance): RedirectResponse
    {
        abort_if($receipt->status === Receipt::PROCESSING, 409, 'Nota masih dibaca AI.');

        $data = $request->validate([
            'supplier_id' => ['nullable', 'integer'],
            'receipt_date' => ['required', 'date_format:Y-m-d', 'after:2015-01-01', 'before_or_equal:'.now()->addDay()->toDateString()],
            'action' => ['required', Rule::in(['simpan', 'setujui'])],
            'items' => ['required', 'array', 'min:1', 'max:'.config('pasok.receipt.max_items')],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.name' => ['required', 'string', 'max:100'],
            'items.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'items.*.category' => ['nullable', Rule::in(array_keys(config('pasok.categories')))],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'max:1000000'],
            'items.*.unit' => ['required', 'string', 'max:20'],
            'items.*.total_price' => ['required', 'integer', 'min:0', 'max:10000000000'],
        ], [
            'items.required' => 'Nota harus berisi minimal satu barang.',
            'items.*.name.required' => 'Nama barang wajib diisi.',
            'items.*.quantity.gt' => 'Jumlah harus lebih dari 0.',
        ]);

        $supplier = null;

        if (! empty($data['supplier_id'])) {
            $supplier = Supplier::query()->visibleTo($receipt->business_id)->whereKey($data['supplier_id'])->first();

            if (! $supplier) {
                return back()->withErrors(['supplier_id' => 'Pemasok tidak ditemukan.'])->withInput();
            }
        }

        if ($data['action'] !== 'setujui') {
            $pipeline->review($receipt, $supplier, $data['receipt_date'], $data['items'], false);

            return back()->with('status', 'Perubahan disimpan.');
        }

        $month = CarbonImmutable::parse($data['receipt_date'])->startOfMonth();
        $before = $compliance->forMonth($receipt->business_id, $month)['groups'];

        $pipeline->review($receipt, $supplier, $data['receipt_date'], $data['items'], true);

        $after = $compliance->forMonth($receipt->business_id, $month)['groups'];

        $impact = collect($after)
            ->filter(fn ($group, $key) => $group['total_kg'] != ($before[$key]['total_kg'] ?? 0) || $group['percent'] !== ($before[$key]['percent'] ?? null))
            ->map(fn ($group, $key) => [
                'label' => $group['label'],
                'quota' => $group['quota'],
                'before' => $before[$key]['percent'] ?? null,
                'after' => $group['percent'],
                'added_kg' => round($group['total_kg'] - ($before[$key]['total_kg'] ?? 0), 2),
                'added_local_kg' => round($group['local_kg'] - ($before[$key]['local_kg'] ?? 0), 2),
            ])
            ->values()
            ->all();

        $unknown = $receipt->items()->whereNotNull('category')->where('origin', ReceiptItem::UNKNOWN)->count();

        return redirect()->route('dashboard', ['bulan' => $month->format('Y-m')])
            ->with('status', 'Nota bertanggal '.$receipt->receipt_date->translatedFormat('j F Y').' disetujui dan dihitung ke bulan '.$month->translatedFormat('F Y').', sesuai tanggal di nota.')
            ->with('impact', ['month' => $month->format('Y-m'), 'groups' => $impact, 'unknown' => $unknown, 'supplier' => $supplier?->name]);
    }

    public function destroy(Receipt $receipt): RedirectResponse
    {
        if ($receipt->image_path) {
            Storage::disk('local')->delete($receipt->image_path);
        }

        $receipt->delete();

        return redirect()->route('receipts.index')->with('status', 'Nota dihapus.');
    }

    public static function samples(): array
    {
        $dir = database_path(self::SAMPLE_DIR);

        if (! is_dir($dir)) {
            return [];
        }

        return collect(scandir($dir) ?: [])
            ->filter(fn ($file) => preg_match('/^[a-z0-9_-]+\.(jpe?g|png|webp)$/i', $file))
            ->values()
            ->all();
    }

    private static function samplePath(string $sample): ?string
    {
        return in_array($sample, self::samples(), true) ? database_path(self::SAMPLE_DIR.'/'.$sample) : null;
    }

    private function throttle(Request $request): void
    {
        $key = 'unggah-nota:'.$request->user()->business_id;

        abort_if(RateLimiter::tooManyAttempts($key, 30), 429, 'Terlalu banyak nota diunggah. Coba lagi sebentar lagi.');

        RateLimiter::hit($key, 3600);
    }

    private function queue(Request $request, string $path): RedirectResponse
    {
        $receipt = Receipt::query()->create([
            'uploaded_by' => $request->user()->id,
            'image_path' => $path,
            'status' => Receipt::PROCESSING,
            'receipt_date' => now()->toDateString(),
        ]);

        ProcessReceipt::dispatchAfterResponse($receipt->id);

        return redirect()->route('receipts.show', $receipt);
    }
}
