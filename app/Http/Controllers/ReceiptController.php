<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessReceipt;
use App\Models\Receipt;
use App\Models\Supplier;
use App\Services\ProductCatalog;
use App\Services\ReceiptPipeline;
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

    public function create(): View
    {
        return view('app.receipts.create', ['samples' => self::samples()]);
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

    public function update(Request $request, Receipt $receipt, ReceiptPipeline $pipeline): RedirectResponse
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

        $pipeline->review($receipt, $supplier, $data['receipt_date'], $data['items'], $data['action'] === 'setujui');

        return $data['action'] === 'setujui'
            ? redirect()->route('dashboard', ['bulan' => $receipt->receipt_date->format('Y-m')])->with('status', 'Nota disetujui dan sudah dihitung ke kepatuhan bulan '.$receipt->receipt_date->translatedFormat('F Y').'.')
            : back()->with('status', 'Perubahan disimpan.');
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
