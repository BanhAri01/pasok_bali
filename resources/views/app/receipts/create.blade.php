<x-app.layout title="Foto nota">
    <div class="mx-auto max-w-2xl">
        <h1 class="font-display text-3xl font-black text-daun-tua">Foto nota belanja</h1>
        <p class="mt-2 text-abu">Nota cetak, nota tulisan tangan pasar, atau faktur pemasok. Pastikan seluruh nota terlihat, terang, dan tidak miring. AI akan membaca barang, jumlah, satuan, dan harga; Anda tetap memeriksa sebelum disetujui.</p>

        <form method="POST" action="{{ route('receipts.store') }}" enctype="multipart/form-data" class="mt-6 border-2 border-dashed border-daun/40 bg-kertas p-6"
              x-data="{ preview: null, sending: false }" @submit="sending = true">
            @csrf
            <label for="foto" class="block cursor-pointer text-center">
                <template x-if="preview"><img :src="preview" alt="Pratinjau nota" class="mx-auto max-h-80 rounded"></template>
                <span x-show="!preview" class="block py-8">
                    <span class="block font-display text-xl font-bold">Ketuk untuk memotret atau memilih foto</span>
                    <span class="mt-1 block text-sm text-abu">JPG, PNG, atau WEBP · maksimal 8 MB</span>
                </span>
            </label>
            <input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp" capture="environment" required class="sr-only"
                   @change="const f = $event.target.files[0]; preview = f ? URL.createObjectURL(f) : null">
            <button class="mt-5 w-full rounded bg-tinta px-5 py-3 font-bold text-kertas hover:bg-taum disabled:opacity-60" :disabled="sending || !preview" x-text="sending ? 'Mengunggah…' : 'Baca nota dengan AI'"></button>
        </form>

        @if ($samples)
            <section class="mt-8">
                <h2 class="font-display text-lg font-bold">Tidak punya nota? Pakai nota contoh</h2>
                <p class="text-sm text-abu">Nota fiktif untuk mencoba pembacaan AI.</p>
                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ($samples as $sample)
                        <form method="POST" action="{{ route('receipts.sample', $sample) }}">
                            @csrf
                            <button class="group block w-full overflow-hidden rounded border-2 border-tinta bg-kertas text-left hover:border-daun">
                                <img src="{{ route('receipts.sample-image', $sample) }}" alt="Nota contoh {{ $loop->iteration }}" loading="lazy" class="aspect-[3/4] w-full object-cover object-top">
                                <span class="block px-2 py-1.5 text-xs font-semibold group-hover:text-daun">Pakai contoh {{ $loop->iteration }}</span>
                            </button>
                        </form>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-app.layout>
