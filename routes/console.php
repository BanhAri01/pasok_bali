<?php

use App\Models\Receipt;
use App\Models\WaConversation;
use App\Models\WaMessage;
use App\Services\DemoSandbox;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('pasok:rapikan', function (DemoSandbox $sandbox) {
    $demos = $sandbox->purgeExpired();

    $images = 0;
    Receipt::query()
        ->whereNotNull('image_path')
        ->where('status', Receipt::APPROVED)
        ->where('approved_at', '<', now()->subDays((int) config('pasok.receipt.keep_image_days')))
        ->select(['id', 'image_path'])
        ->chunkById(200, function ($receipts) use (&$images) {
            foreach ($receipts as $receipt) {
                Storage::disk('local')->delete($receipt->image_path);
                $receipt->update(['image_path' => null]);
                $images++;
            }
        });

    $messages = WaMessage::query()->where('channel', '!=', 'wa')->where('created_at', '<', now()->subDays(2))->delete();
    WaConversation::query()->where('expires_at', '<', now())->delete();

    $this->info("Demo dihapus: {$demos}. Foto nota lama dihapus: {$images}. Pesan simulator dihapus: {$messages}.");
})->purpose('Hapus akun demo kedaluwarsa, foto nota lama, dan pesan simulator');

Schedule::command('pasok:rapikan')->hourly()->withoutOverlapping();
