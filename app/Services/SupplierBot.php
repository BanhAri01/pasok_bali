<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\SupplyRequest;
use App\Models\WaConversation;
use App\Support\Phone;
use App\Support\Rupiah;
use App\Support\Text;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SupplierBot
{
    public const HELP = "Halo, ini Pasok Bali. Kami menghubungkan petani, peternak, nelayan, dan UMKM Bali dengan hotel dan restoran.\n\nCara daftar sebagai pemasok, kirim misalnya:\nDAFTAR telur ayam 200 krat seminggu, Tabanan, Rp52.000/krat, nama Peternakan Sari\n\nJika ada permintaan dari hotel/restoran, balas 1 untuk terima atau 2 untuk tolak. Ada pertanyaan? Tulis saja. Ketik SELESAI untuk menutup percakapan.";

    public const CLOSED = 'Halo, ini Pasok Bali. Ketik PASOK untuk mulai mengobrol dengan kami.';

    public const ENDED = 'Percakapan ditutup. Suksma! Ketik PASOK kapan saja untuk mulai lagi.';

    public const EXAMPLE = 'DAFTAR telur ayam 200 krat seminggu, Tabanan, Rp52.000/krat, nama Peternakan Sari';

    private const SESSION = 'sesi';

    private const CONFIRMING = 'konfirmasi_daftar';

    public static function stripKeyword(string $text): string
    {
        return trim((string) preg_replace('/^\s*pasok(?:\s*bali)?\b[\s:,.\-]*/iu', '', $text));
    }

    public function __construct(
        private Messenger $messenger,
        private SupplierRegistrationParser $parser,
        private ReceiptPipeline $pipeline,
        private SupplierAssistant $assistant,
    ) {}

    public function handle(string $phone, string $text, ?int $ownerBusinessId = null): array
    {
        $opening = (bool) preg_match('/^\s*pasok\b/iu', $text);
        $text = self::stripKeyword($text);
        $key = Text::key($text);
        $supplier = Supplier::query()->where('phone', $phone)->first();
        $conversation = WaConversation::query()->where('phone', $phone)->where('expires_at', '>', now())->first();
        $answering = in_array($key, ['1', '2'], true) && $supplier !== null;

        if (! $conversation && ! $opening) {
            return $this->send($phone, $answering ? $this->respond($supplier, $key === '1') : $this->closed($phone));
        }

        $conversation = $this->keepAlive($phone, $conversation, $ownerBusinessId);
        $confirming = $conversation->state === self::CONFIRMING;

        return $this->send($phone, match (true) {
            in_array($key, ['selesai', 'stop', 'keluar', 'tutup'], true) => $this->end($conversation),
            $confirming && in_array($key, ['ya', 'iya', 'y', 'ok', 'oke', 'benar'], true) => $this->confirm($phone, $conversation, $ownerBusinessId),
            $confirming && in_array($key, ['batal', 'tidak', 'salah'], true) => $this->cancel($conversation),
            $answering => $this->respond($supplier, $key === '1'),
            str_starts_with($key, 'daftar') || str_starts_with($key, 'jual') => $this->register($phone, $text, $ownerBusinessId),
            $key === '' || in_array($key, ['menu', 'bantuan', 'help', 'halo', 'hai', 'mulai'], true) => [self::HELP],
            default => $this->ask($phone, $text, $supplier, $ownerBusinessId),
        });
    }

    private function send(string $phone, array $replies): array
    {
        foreach ($replies as $reply) {
            $this->messenger->send($phone, $reply);
        }

        return $replies;
    }

    private function closed(string $phone): array
    {
        return Cache::add('wa-tutup:'.$phone, true, now()->addHours(12)) ? [self::CLOSED] : [];
    }

    private function keepAlive(string $phone, ?WaConversation $conversation, ?int $ownerBusinessId): WaConversation
    {
        $expires = now()->addMinutes((int) config('pasok.whatsapp.session_minutes', 30));

        if ($conversation) {
            $conversation->update(['expires_at' => $expires]);

            return $conversation;
        }

        return WaConversation::query()->updateOrCreate(
            ['phone' => $phone],
            ['state' => self::SESSION, 'payload' => null, 'business_id' => $ownerBusinessId, 'expires_at' => $expires],
        );
    }

    private function end(WaConversation $conversation): array
    {
        $conversation->delete();

        return [self::ENDED];
    }

    private function ask(string $phone, string $text, ?Supplier $supplier, ?int $ownerBusinessId): array
    {
        $reply = $this->assistant->reply($phone, $text, $supplier);

        return match (true) {
            $reply === null => ["Maaf, pertanyaan belum bisa kami jawab sekarang.\n\n".self::HELP],
            $reply['intent'] === SupplierAssistant::DAFTAR => $this->register($phone, $text, $ownerBusinessId),
            default => [$reply['answer']],
        };
    }
    private function register(string $phone, string $text, ?int $ownerBusinessId): array
    {
        $parsed = $this->parser->parse($text);

        if (! $parsed) {
            return ["Data belum terbaca. Tulis produk, jumlah per minggu, dan kabupaten, contohnya:\n".self::EXAMPLE];
        }

        if ($parsed['regency'] === null) {
            return ['Mohon sebutkan kabupaten/kota lokasi produksi Anda (misalnya Tabanan, Bangli, Buleleng). Kirim ulang pesan DAFTAR lengkap dengan kabupatennya.'];
        }

        WaConversation::query()->updateOrCreate(
            ['phone' => $phone],
            ['state' => self::CONFIRMING, 'payload' => $parsed, 'business_id' => $ownerBusinessId, 'expires_at' => now()->addMinutes(30)],
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

            $conversation->update(['state' => self::SESSION, 'payload' => null]);

            return $supplier;
        });

        $this->pipeline->refreshSupplierOrigins($supplier);

        return ["Tersimpan, suksma! {$supplier->name} sudah terdaftar di Pasok Bali sebagai pemasok (status: pernyataan pemasok, menunggu verifikasi).\n\nJika hotel/restoran mengirim permintaan, Anda akan menerima pesan di nomor ini. Balas 1 untuk terima, 2 untuk tolak."];
    }

    private function cancel(WaConversation $conversation): array
    {
        $conversation->update(['state' => self::SESSION, 'payload' => null]);

        return ['Dibatalkan. Kirim ulang pesan DAFTAR kapan saja.'];
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
            return ['Belum ada permintaan yang menunggu jawaban. Ketik DAFTAR untuk memperbarui produk Anda.'];
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
