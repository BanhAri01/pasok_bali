<x-app.layout title="Nota">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h1 class="font-display text-3xl font-black text-daun-tua">Nota belanja</h1>
        <a href="{{ route('receipts.create') }}" class="rounded-xl bg-daun px-5 py-3 font-bold text-kertas hover:bg-daun-tua">+ Foto nota</a>
    </div>

    <nav class="mt-4 flex flex-wrap gap-2 text-sm">
        @foreach ([null => 'Semua', 'perlu_cek' => 'Perlu dicek', 'disetujui' => 'Disetujui', 'diproses' => 'Sedang dibaca'] as $value => $label)
            <a href="{{ route('receipts.index', array_filter(['status' => $value])) }}" @class(['rounded-full px-3 py-1.5 font-semibold', 'bg-daun text-kertas' => $status === ($value ?: null), 'bg-kertas text-abu ring-1 ring-garis' => $status !== ($value ?: null)])>{{ $label }}</a>
        @endforeach
    </nav>

    <div class="mt-4 overflow-x-auto rounded-2xl border border-garis bg-kertas">
        <table class="w-full min-w-[640px] text-sm">
            <thead class="bg-santan text-left text-abu">
                <tr>
                    <th class="px-4 py-3 font-semibold">Tanggal</th>
                    <th class="px-4 py-3 font-semibold">Pemasok</th>
                    <th class="px-4 py-3 font-semibold">Barang</th>
                    <th class="px-4 py-3 text-right font-semibold">Total</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-garis">
                @forelse ($receipts as $receipt)
                    <tr class="hover:bg-santan">
                        <td class="px-4 py-3 whitespace-nowrap"><a href="{{ route('receipts.show', $receipt) }}" class="font-semibold text-daun">{{ $receipt->receipt_date?->translatedFormat('j M Y') ?? '–' }}</a></td>
                        <td class="px-4 py-3">{{ $receipt->supplier?->name ?? $receipt->supplier_name_raw ?? '–' }}@unless ($receipt->supplier_id)<span class="ml-1 text-xs text-abu">(belum terdaftar)</span>@endunless</td>
                        <td class="px-4 py-3">{{ $receipt->items_count }} @if ($receipt->review_count)<span class="text-xs font-semibold text-terakota">· {{ $receipt->review_count }} perlu dicek</span>@endif</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">{{ \App\Support\Rupiah::format($receipt->total) }}</td>
                        <td class="px-4 py-3"><span @class(['rounded-full px-2.5 py-0.5 text-xs font-semibold', 'bg-daun-muda text-daun-tua' => $receipt->status === 'disetujui', 'bg-kunyit-muda text-tinta' => $receipt->status !== 'disetujui'])>{{ $receipt->statusLabel() }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-abu">Belum ada nota.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $receipts->links() }}</div>
</x-app.layout>
