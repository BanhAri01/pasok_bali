<x-app.layout title="Daftar">
    <div class="mx-auto max-w-xl">
        <h1 class="font-display text-3xl font-black text-daun-tua">Daftarkan usaha</h1>
        <form method="POST" action="{{ route('register') }}" class="mt-6 grid gap-4 rounded-2xl border border-garis bg-kertas p-6 sm:grid-cols-2">
            @csrf
            <div class="sm:col-span-2">
                <label for="business_name" class="text-sm font-semibold">Nama usaha</label>
                <input id="business_name" name="business_name" value="{{ old('business_name') }}" required maxlength="120" class="mt-1 w-full rounded-lg border border-garis px-3 py-2.5">
            </div>
            <div>
                <label for="business_type" class="text-sm font-semibold">Jenis usaha</label>
                <select id="business_type" name="business_type" required class="mt-1 w-full rounded-lg border border-garis px-3 py-2.5">
                    @foreach ($types as $value => $label)
                        <option value="{{ $value }}" @selected(old('business_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="regency" class="text-sm font-semibold">Kabupaten/kota</label>
                <select id="regency" name="regency" required class="mt-1 w-full rounded-lg border border-garis px-3 py-2.5">
                    @foreach ($regencies as $regency)
                        <option value="{{ $regency }}" @selected(old('regency') === $regency)>{{ $regency }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label for="name" class="text-sm font-semibold">Nama Anda</label>
                <input id="name" name="name" value="{{ old('name') }}" required maxlength="100" class="mt-1 w-full rounded-lg border border-garis px-3 py-2.5">
            </div>
            <div class="sm:col-span-2">
                <label for="email" class="text-sm font-semibold">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="mt-1 w-full rounded-lg border border-garis px-3 py-2.5">
            </div>
            <div>
                <label for="password" class="text-sm font-semibold">Kata sandi</label>
                <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-lg border border-garis px-3 py-2.5">
            </div>
            <div>
                <label for="password_confirmation" class="text-sm font-semibold">Ulangi kata sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-1 w-full rounded-lg border border-garis px-3 py-2.5">
            </div>
            <button class="rounded-xl bg-daun px-5 py-3 font-bold text-kertas hover:bg-daun-tua sm:col-span-2">Buat akun</button>
        </form>
    </div>
</x-app.layout>
