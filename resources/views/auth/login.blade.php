<x-app.layout title="Masuk">
    <div class="mx-auto max-w-md">
        <h1 class="font-display text-3xl font-black text-daun-tua">Masuk</h1>
        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4 rounded-2xl border border-garis bg-kertas p-6">
            @csrf
            <div>
                <label for="email" class="text-sm font-semibold">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-1 w-full rounded-lg border border-garis px-3 py-2.5">
            </div>
            <div>
                <label for="password" class="text-sm font-semibold">Kata sandi</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="mt-1 w-full rounded-lg border border-garis px-3 py-2.5">
            </div>
            <label class="flex items-center gap-2 text-sm text-abu"><input type="checkbox" name="ingat" value="1"> Ingat saya</label>
            <button class="w-full rounded-xl bg-daun px-5 py-3 font-bold text-kertas hover:bg-daun-tua">Masuk</button>
        </form>
        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm">
            <a href="{{ route('register') }}" class="font-semibold text-daun">Belum punya akun? Daftar</a>
            <form method="POST" action="{{ route('demo') }}">
                @csrf
                <button class="font-semibold text-abu underline hover:text-tinta">Coba demo tanpa daftar</button>
            </form>
        </div>
    </div>
</x-app.layout>
