<?php

namespace App\Http\Controllers;

use App\Services\Messenger;
use App\Services\SupplierBot;
use App\Support\Phone;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class WhatsAppWebhookController extends Controller
{
    public function __invoke(Request $request, string $secret, Messenger $messenger): JsonResponse
    {
        $inbound = $this->inbound($request, $secret);

        if ($inbound instanceof JsonResponse) {
            return $inbound;
        }

        return $this->accept($messenger, ...$inbound);
    }

    public function forward(Request $request, string $secret, Messenger $messenger, SupplierBot $bot): JsonResponse
    {
        $inbound = $this->inbound($request, $secret);

        if ($inbound instanceof JsonResponse || ! $bot->claims($inbound[0], $inbound[1])) {
            return response()->json(['claimed' => false]);
        }

        $this->accept($messenger, ...$inbound);

        return response()->json(['claimed' => true]);
    }

    private function inbound(Request $request, string $secret): array|JsonResponse
    {
        $expected = (string) config('pasok.whatsapp.webhook_secret');
        abort_if($expected === '' || ! hash_equals($expected, $secret), 404);

        $device = Phone::normalize(config('pasok.whatsapp.device'));

        if ($device && Phone::normalize((string) $request->input('device')) !== $device) {
            return $this->ignored('device');
        }

        $rawSender = (string) ($request->input('pengirim') ?: $request->input('sender'));

        if ($request->boolean('isgroup') || filled($request->input('member')) || str_contains($rawSender, '@') || str_contains($rawSender, '-')) {
            return $this->ignored('group');
        }

        $sender = Phone::normalize($rawSender);
        $text = trim((string) ($request->input('message') ?: $request->input('pesan')));

        if (! $sender || $text === '') {
            return $this->ignored('empty');
        }

        $inboxId = (string) ($request->input('inboxid') ?: $request->input('id'));

        return [$sender, $text, $inboxId !== '' && $inboxId !== '0' ? 'fonnte:'.mb_substr($inboxId, 0, 90) : null];
    }

    private function accept(Messenger $messenger, string $sender, string $text, ?string $externalId): JsonResponse
    {
        $key = 'wa-masuk:'.$sender;

        if (RateLimiter::tooManyAttempts($key, (int) config('pasok.whatsapp.per_minute'))) {
            return $this->ignored('rate');
        }

        RateLimiter::hit($key, 60);

        try {
            $messenger->record($sender, $text, $externalId);
        } catch (UniqueConstraintViolationException) {
            return $this->ignored('duplicate');
        }

        dispatch(fn () => app(SupplierBot::class)->handle($sender, $text))->afterResponse();

        return response()->json(['status' => 'accepted']);
    }

    private function ignored(string $reason): JsonResponse
    {
        return response()->json(['status' => 'ignored', 'reason' => $reason]);
    }
}
