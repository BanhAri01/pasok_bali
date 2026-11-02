<?php

namespace App\Services;

use App\Models\WaMessage;
use App\Support\Phone;
use Illuminate\Support\Facades\Http;
use Throwable;

class Messenger
{
    public function send(string $phone, string $body): WaMessage
    {
        $channel = Phone::channel($phone);

        $message = WaMessage::query()->create([
            'direction' => WaMessage::OUT,
            'phone' => $phone,
            'body' => $body,
            'channel' => $channel,
            'status' => 'queued',
        ]);

        if ($channel !== 'wa' || config('pasok.whatsapp.driver') !== 'fonnte') {
            $message->update(['status' => 'logged']);

            return $message;
        }

        if ($this->sentToday() >= (int) config('pasok.whatsapp.daily_outbound_limit')) {
            $message->update(['status' => 'skipped', 'error' => 'Batas pesan harian tercapai']);

            return $message;
        }

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->withHeaders(['Authorization' => (string) config('pasok.whatsapp.fonnte_token')])
                ->post((string) config('pasok.whatsapp.fonnte_url'), [
                    'target' => $phone,
                    'message' => $body,
                    'countryCode' => '62',
                ]);

            $ok = $response->successful() && $response->json('status') === true;
            $message->update([
                'status' => $ok ? 'sent' : 'failed',
                'error' => $ok ? null : mb_substr((string) ($response->json('reason') ?? $response->status()), 0, 255),
            ]);
        } catch (Throwable $exception) {
            $message->update(['status' => 'failed', 'error' => mb_substr($exception->getMessage(), 0, 255)]);
        }

        return $message;
    }

    public function record(string $phone, string $body, ?string $externalId = null): WaMessage
    {
        return WaMessage::query()->create([
            'direction' => WaMessage::IN,
            'phone' => $phone,
            'body' => mb_substr($body, 0, 1000),
            'channel' => Phone::channel($phone),
            'status' => 'received',
            'external_id' => $externalId,
        ]);
    }

    private function sentToday(): int
    {
        return WaMessage::query()
            ->where('direction', WaMessage::OUT)
            ->where('channel', 'wa')
            ->where('status', 'sent')
            ->where('created_at', '>=', now()->startOfDay())
            ->count();
    }
}
