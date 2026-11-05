<?php

namespace App\Http\Controllers;

use App\Models\SupplyRequest;
use App\Models\WaMessage;
use App\Services\Messenger;
use App\Services\SupplierBot;
use App\Support\Phone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SimulatorController extends Controller
{
    public function index(Request $request): View
    {
        $phone = $this->phone($request);
        $business = $request->attributes->get('business');

        $requests = SupplyRequest::query()
            ->with(['supplier:id,name,phone', 'product:id,name'])
            ->whereHas('supplier', fn ($query) => $query->where('phone', 'like', Phone::SAMPLE_PREFIX.'%')->orWhere('owner_business_id', $business->id))
            ->latest()
            ->limit(10)
            ->get();

        return view('app.simulator', [
            'phone' => $phone,
            'requests' => $requests,
            'messages' => WaMessage::query()->where('phone', $phone)->where('created_at', '>=', now()->subDays(2))->oldest('id')->limit(200)->get(),
        ]);
    }

    public function send(Request $request, SupplierBot $bot, Messenger $messenger): RedirectResponse
    {
        $data = $request->validate(['text' => ['required', 'string', 'max:500']]);
        $this->throttle($request);

        $phone = $this->phone($request);
        $messenger->record($phone, $data['text']);
        $bot->handle($phone, $data['text'], $request->attributes->get('business')->id);

        return redirect()->route('simulator.index', ['#chat']);
    }

    public function reply(Request $request, SupplyRequest $supplyRequest, SupplierBot $bot): RedirectResponse
    {
        $data = $request->validate(['answer' => ['required', Rule::in(['1', '2'])]]);
        $this->throttle($request);

        $phone = (string) $supplyRequest->supplier?->phone;
        $business = $request->attributes->get('business');

        abort_unless($phone !== '' && (Phone::channel($phone) === 'contoh' || $supplyRequest->supplier->owner_business_id === $business->id), 404);

        $bot->answer($supplyRequest, $data['answer'] === '1');

        return redirect()->route('simulator.index')->with('status', 'Balasan pemasok terkirim lewat simulator.');
    }

    private function phone(Request $request): string
    {
        $phone = (string) $request->session()->get('simulator_phone', '');

        if ($phone === '' || Phone::channel($phone) !== 'simulator') {
            $phone = Phone::simulator();
            $request->session()->put('simulator_phone', $phone);
        }

        return $phone;
    }

    private function throttle(Request $request): void
    {
        $key = 'simulator:'.$request->user()->id;
        abort_if(RateLimiter::tooManyAttempts($key, 20), 429, 'Terlalu cepat. Tunggu sebentar.');
        RateLimiter::hit($key, 60);
    }
}
