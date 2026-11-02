<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesMonth;
use App\Models\SupplierProduct;
use App\Models\SupplyRequest;
use App\Services\AiWriter;
use App\Services\Messenger;
use App\Services\RecommendationEngine;
use App\Support\Phone;
use App\Support\Text;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class RecommendationController extends Controller
{
    use ResolvesMonth;

    public function index(Request $request, RecommendationEngine $engine): View
    {
        $business = $request->attributes->get('business');
        $month = $this->month($request);

        return view('app.recommendations', [
            'month' => $month,
            'recommendations' => $engine->forMonth($business, $month),
            'requests' => SupplyRequest::query()->with(['supplier:id,name,regency', 'product:id,name'])->latest()->limit(20)->get(),
        ]);
    }

    public function draft(Request $request, AiWriter $writer): JsonResponse
    {
        $business = $request->attributes->get('business');
        [$offer, $quantity, $unit] = $this->offer($request, $business->id);

        $draft = $writer->supplyMessage($business, $offer->supplier, $offer->product, $quantity, $unit);

        return response()->json($draft);
    }

    public function send(Request $request, Messenger $messenger): RedirectResponse
    {
        $business = $request->attributes->get('business');
        [$offer, $quantity, $unit] = $this->offer($request, $business->id);

        $message = trim((string) $request->validate(['message' => ['required', 'string', 'min:20', 'max:700']])['message']);

        $key = 'permintaan:'.$business->id;
        abort_if(RateLimiter::tooManyAttempts($key, 20), 429, 'Terlalu banyak permintaan hari ini.');
        RateLimiter::hit($key, 86400);

        $exists = SupplyRequest::query()
            ->where('supplier_id', $offer->supplier_id)
            ->where('product_id', $offer->product_id)
            ->where('status', SupplyRequest::WAITING)
            ->exists();

        if ($exists) {
            return back()->withErrors(['permintaan' => 'Permintaan ke pemasok ini untuk produk yang sama masih menunggu jawaban.']);
        }

        SupplyRequest::query()->create([
            'supplier_id' => $offer->supplier_id,
            'product_id' => $offer->product_id,
            'quantity_per_week' => $quantity,
            'unit' => $unit,
            'status' => SupplyRequest::WAITING,
            'message' => $message,
        ]);

        if ($offer->supplier->phone) {
            $messenger->send($offer->supplier->phone, $message."\n\n— Dikirim lewat Pasok Bali untuk {$business->name}. Balas 1 untuk TERIMA, 2 untuk TOLAK.");
        }

        $channel = $offer->supplier->phone ? Phone::channel($offer->supplier->phone) : 'none';
        $note = match ($channel) {
            'wa' => 'Permintaan terkirim ke WhatsApp pemasok.',
            'none' => 'Permintaan tercatat. Pemasok ini belum punya nomor WhatsApp.',
            default => 'Permintaan tercatat ke pemasok contoh. Buka Simulator WA untuk mencoba membalas sebagai pemasok.',
        };

        return redirect()->route('recommendations.index')->with('status', $note);
    }

    private function offer(Request $request, int $businessId): array
    {
        $data = $request->validate([
            'offer_id' => ['required', 'integer'],
            'quantity' => ['required', 'numeric', 'gt:0', 'max:100000'],
            'unit' => ['required', 'string', 'max:20'],
        ]);

        $offer = SupplierProduct::query()
            ->with(['supplier', 'product'])
            ->whereKey($data['offer_id'])
            ->where('origin', SupplierProduct::BALI)
            ->whereHas('supplier', fn ($query) => $query->visibleTo($businessId)->where('active', true))
            ->firstOrFail();

        return [$offer, round((float) $data['quantity'], 1), Text::unit($data['unit'])];
    }
}
