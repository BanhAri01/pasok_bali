<?php

namespace App\Jobs;

use App\Models\Receipt;
use App\Services\ReceiptPipeline;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ProcessReceipt implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public int $receiptId) {}

    public function handle(ReceiptPipeline $pipeline): void
    {
        $receipt = Receipt::query()->find($this->receiptId);

        if (! $receipt || $receipt->status !== Receipt::PROCESSING) {
            return;
        }

        try {
            $pipeline->process($receipt);
        } catch (Throwable $exception) {
            report($exception);
            $receipt->update([
                'status' => Receipt::NEEDS_CHECK,
                'note' => 'Terjadi kendala saat membaca nota. Isi barang secara manual.',
                'receipt_date' => $receipt->receipt_date ?? now()->toDateString(),
            ]);
        }
    }
}
