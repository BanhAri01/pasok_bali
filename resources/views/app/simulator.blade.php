<x-app.layout title="Simulator WA">
    <h1 class="font-display text-3xl font-semibold text-daun-tua">Simulator WhatsApp pemasok</h1>
    <p class="mt-1 text-abu">Coba alur WhatsApp tanpa nomor sungguhan. Pesan di sini tidak dikirim ke WhatsApp mana pun dan dihapus otomatis setelah 2 hari.</p>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section id="chat" class="flex flex-col border border-garis bg-kertas rounded-2xl">
            <header class="bg-daun-tua px-4 py-3 text-kertas">
                <p class="font-bold">Daftar sebagai pemasok baru</p>
                <p class="text-xs opacity-80">Anda berperan sebagai petani/peternak yang mengirim WA ke Pasok Bali</p>
            </header>
            <div class="flex-1 space-y-2 overflow-y-auto bg-santan p-4" style="max-height: 28rem">
                @forelse ($messages as $message)
                    <div @class(['max-w-[85%] whitespace-pre-line px-3 py-2 text-sm', 'ml-auto bg-daun-muda' => $message->direction === 'in', 'bg-kertas' => $message->direction === 'out'])>{{ $message->body }}</div>
                @empty
                    <p class="text-center text-sm text-abu">Belum ada pesan. Contoh: <em>PASOK DAFTAR sayur kangkung 50 ikat seminggu, Gianyar, nama Tani Mekar</em></p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('simulator.send') }}" class="flex gap-2 border-t border-garis p-3">
                @csrf
                <input name="text" required maxlength="500" placeholder="Tulis pesan…" class="min-w-0 flex-1 rounded-lg border border-garis px-3 py-2" aria-label="Pesan">
                <button class="rounded-full bg-daun-tua px-4 py-2 font-bold text-kertas">Kirim</button>
            </form>
        </section>

        <section>
            <h2 class="font-display text-xl font-semibold">Permintaan yang masuk ke pemasok</h2>
            <p class="text-sm text-abu">Balas sebagai pemasok contoh untuk melihat status permintaan berubah.</p>
            <div class="mt-3 space-y-3">
                @forelse ($requests as $request)

                    <article class="border border-garis bg-kertas p-4 rounded-2xl">
                        <div class="flex items-start justify-between gap-2">
                            <p class="font-semibold">{{ $request->supplier->name }}</p>
                            <span @class(['rounded-full px-2.5 py-0.5 text-xs font-semibold', 'bg-daun text-kertas' => $request->status === 'diterima', 'bg-terakota-muda text-terakota' => $request->status === 'ditolak', 'bg-kunyit-muda' => $request->status === 'menunggu'])>{{ ucfirst($request->status) }}</span>
                        </div>
                        <div class="mt-2 space-y-2 rounded-lg bg-santan p-3">
                            <div class="max-w-[90%] whitespace-pre-line bg-kertas px-3 py-2 text-xs">{{ $request->message }}</div>
                            @if ($request->status !== 'menunggu')
                                <div class="ml-auto w-fit bg-daun-muda px-3 py-2 text-xs">{{ $request->status === 'diterima' ? '1' : '2' }}</div>
                            @endif
                        </div>
                        @if ($request->status === 'menunggu')
                            <div class="mt-3 flex gap-2">
                                <form method="POST" action="{{ route('simulator.reply', $request) }}">@csrf<input type="hidden" name="answer" value="1"><button class="rounded-full bg-daun-tua px-3 py-1.5 text-sm font-bold text-kertas">Balas 1 (terima)</button></form>
                                <form method="POST" action="{{ route('simulator.reply', $request) }}">@csrf<input type="hidden" name="answer" value="2"><button class="rounded-lg border border-garis px-3 py-1.5 text-sm font-semibold">Balas 2 (tolak)</button></form>
                            </div>
                        @endif
                    </article>
                @empty
                    <p class="border border-garis bg-kertas p-6 text-center text-sm text-abu rounded-2xl">Belum ada permintaan. Kirim dari halaman <a href="{{ route('recommendations.index') }}" class="font-semibold text-daun">Rekomendasi</a>.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app.layout>
