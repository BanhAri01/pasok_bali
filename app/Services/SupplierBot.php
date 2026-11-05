<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\SupplyRequest;
use App\Models\WaConversation;
use App\Support\Phone;
use App\Support\Rupiah;
use App\Support\Text;
use Illuminate\Support\Facades\DB;

class SupplierBot
{
    public const HELP = "Halo, ini Pasok Bali. Kami menghubungkan petani, peternak, nelayan, dan UMKM Bali dengan hotel dan restoran.\n\nCara daftar sebagai pemasok, kirim misalnya:\nPASOK DAFTAR telur ayam 200 krat seminggu, Tabanan, Rp52.000/krat, nama Peternakan Sari\n\nJika ada permintaan dari hotel/restoran, balas 1 untuk terima atau 2 untuk tolak.";

    public const EXAMPLE = 'PASOK DAFTAR telur ayam 200 krat seminggu, Tabanan, Rp52.000/krat, nama Peternakan Sari';

    public static function stripKeyword(string $text): string
    {
        return trim((string) preg_replace('/^\s*pasok(?:\s*bali)?\b[\s:,.\-]*/iu', '', $text));
    }

    public function claims(string $phone, string $text): bool
    {
        if (preg_match('/^\s*pasok\b/iu', $text)) {
            return true;
        }

        if (WaConversation::query()->where('phone', $phone)->where('expires_at', '>', now())->exists()) {
            return true;
        }

        return in_array(Text::key($text), ['1', '2'], true)
            && SupplyRequest::query()->where('status', SupplyRequest::WAITING)->whereHas('supplier', fn ($query) => $query->where('phone', $phone))->exists();
    }

    public function __construct(
        private Messenger $messenger,
        private SupplierRegistrationParser $parser,
        private ReceiptPipeline $pipeline,
    ) {}

    public function handle(string $phone, string $text, ?int $ownerBusinessId = null): array
    {
        $text = self::stripKeyword($text);
        $key = Text::key($text);
        $supplier = Supplier::query()->where('phone', $phone)->first();
        $conversation = WaConversation::query()->where('phone', $phone)->where('expires_at', '>', now())->first();

        $replies = match (true) {
            $conversation?->state === 'konfirmasi_daftar' && in_array($key, ['ya', 'iya', 'y', 'ok', 'oke', 'benar'], true) => $this->confirm($phone, $conversation, $ownerBusinessId),
            in_array($key, ['batal', 'tidak', 'salah'], true) && $conversation !== null => $this->cancel($conversation),
            in_array($key, ['1', '2'], true) && $supplier !== null => $this->respond($supplier, $key === '1'),
            str_starts_with($key, 'daftar') || str_starts_with($key, 'jual') => $this->register($phone, $text, $ownerBusinessId),
            default => [self::HELP],
        };

        foreach ($replies as $reply) {
            $this->messenger->send($phone, $reply);
        }

        return $replies;
    }

    private function register(string $phone, string $text, ?int $ownerBusinessId): array
    {
        $parsed = $this->parser->parse($text);

        if (! $parsed) {
            return ["Data belum terbaca. Tulis produk, jumlah per minggu, dan kabupaten, contohnya:\n".self::EXAMPLE];
        }

        if ($parsed['regency'] === null) {
            return ['Mohon sebutkan kabupaten/kota lokasi produksi Anda (misalnya Tabanan, Bangli, Buleleng). Kirim ulang pesan PASOK DAFTAR lengkap dengan kabupatennya.'];
        }

        WaConversation::query()->updateOrCreate(
            ['phone' => $phone],
            ['state' => 'konfirmasi_daftar', 'payload' => $parsed, 'business_id' => $ownerBusinessId, 'expires_at' => now()->addMinutes(30)],
        );

        $lines = collect($parsed['products'])->map(function ($product) use ($parsed) {
            $capacity = $product['capacity_per_week'] ? Rupiah::number($product['capacity_per_week']).' '.$product['unit'].'/minggu' : 'jumlah belum disebut';
            $price = $product['price'] ? ', '.Rupiah::format($product['price']).'/'.($product['price_unit'] ?: $product['unit']) : '';
            $origin = $parsed['regency'] !== 'luar_bali' && $product['produced_in_bali'] ? 'produksi Bali' : 'dari luar Bali';

            return "- {$product['product_name']}: {$capacity}{$price} ({$origin})";
        })->implode("\n");

        $name = $parsed['name'] ?: 'Pemasok '.$this->suffix($phone);
        $regency = $parsed['regency'] === 'luar_bali' ? 'Luar Bali' : $parsed['regency'];

        return ["Mohon dicek:\nNama: {$name}\nLokasi: {$regency}\n{$lines}\n\nBalas YA untuk simpan, atau BATAL bila salah."];
    }

    private function confirm(string $phone, WaConversation $conversation, ?int $ownerBusinessId): array
    {
        $data = $conversation->payload;
        $outside = $data['regency'] === 'luar_bali';

        $supplier = DB::transaction(function () use ($phone, $data, $outside, $conversation, $ownerBusinessId) {
            $supplier = Supplier::query()->firstOrNew(['phone' => $phone]);
            $isSimulator = Phone::channel($phone) !== 'wa';

            $supplier->fill([
                'name' => $data['name'] ?: ($supplier->name ?: 'Pemasok '.$this->suffix($phone)),
                'regency' => $outside ? null : $data['regency'],
                'is_demo' => $isSimulator,
                'owner_business_id' => $isSimulator ? ($conversation->business_id ?? $ownerBusinessId) : null,
                'active' => true,
            ]);
            $supplier->verification ??= Supplier::DECLARED;
            $supplier->save();

            foreach ($data['products'] as $product) {
                SupplierProduct::query()->updateOrCreate(
                    ['supplier_id' => $supplier->id, 'product_id' => $product['product_id']],
                    [
                        'origin' => ! $outside && $product['produced_in_bali'] ? SupplierProduct::BALI : SupplierProduct::OUTSIDE,
                        'capacity_per_week' => $product['capacity_per_week'],
                        'capacity_unit' => $product['unit'],
                        'price_per_unit' => $product['price'],
                        'price_unit' => $product['price_unit'] ?: $product['unit'],
                    ],
                );
            }

            $conversation->delete();

            return $supplier;
        });

        $this->pipeline->refreshSupplierOrigins($supplier);

        return ["Tersimpan, suksma! {$supplier->name} sudah terdaftar di Pasok Bali sebagai pemasok (status: pernyataan pemasok, menunggu verifikasi).\n\nJika hotel/restoran mengirim permintaan, Anda akan menerima pesan di nomor ini. Balas 1 untuk terima, 2 untuk tolak."];
    }

    private function cancel(WaConversation $conversation): array
    {
        $conversation->delete();

        return ['Dibatalkan. Kirim ulang pesan PASOK DAFTAR kapan saja.'];
    }

    private function respond(Supplier $supplier, bool $accept): array
    {
        $request = SupplyRequest::query()
            ->with(['product:id,name', 'business:id,name'])
            ->where('supplier_id', $supplier->id)
            ->where('status', SupplyRequest::WAITING)
            ->oldest()
            ->first();

        if (! $request) {
            return ['Belum ada permintaan yang menunggu jawaban. Ketik PASOK DAFTAR untuk memperbarui produk Anda.'];
        }

        $request->update([
            'status' => $accept ? SupplyRequest::ACCEPTED : SupplyRequest::REJECTED,
            'responded_at' => now(),
        ]);

        $who = $request->business?->name ?? 'pembeli';

        return [$accept
            ? "Suksma! Permintaan {$request->product->name} dari {$who} ditandai DITERIMA. Pihak {$who} akan melanjutkan pembicaraan harga dan jadwal kirim dengan Anda."
            : "Baik, permintaan {$request->product->name} dari {$who} ditandai DITOLAK. Terima kasih atas jawabannya."];
    }

    private function suffix(string $phone): string
    {
        return strtoupper(substr($phone, -4));
    }
}
