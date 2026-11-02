<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Pasok Bali' }} · Pasok Bali</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-santan font-sans text-tinta antialiased">
    @php($business = request()->attributes->get('business'))
    @php($nav = [
        ['dashboard', 'Dasbor', 'dashboard'],
        ['receipts.index', 'Nota', 'receipts.*'],
        ['recommendations.index', 'Rekomendasi', 'recommendations.*'],
        ['suppliers.index', 'Pemasok lokal', 'suppliers.*'],
        ['report', 'Laporan', 'report'],
        ['simulator.index', 'Simulator WA', 'simulator.*'],
    ])

    @if ($business?->is_demo)
        <div class="bg-kunyit px-4 py-2 text-center text-sm font-medium text-tinta">
            Akun demo berisi data contoh fiktif dan akan dihapus otomatis {{ $business->demo_expires_at?->diffForHumans() }}.
        </div>
    @endif

    <header class="border-b border-garis bg-kertas" x-data="{ buka: false }">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
            <a href="{{ $business ? route('dashboard') : route('home') }}" class="flex items-center gap-2">
                <span class="grid size-9 place-items-center rounded-xl bg-daun text-lg font-black text-kunyit">P</span>
                <span class="leading-tight">
                    <span class="block font-display text-lg font-extrabold text-daun-tua">Pasok Bali</span>
                    @if ($business)
                        <span class="block max-w-48 truncate text-xs text-abu">{{ $business->name }}</span>
                    @endif
                </span>
            </a>

            @if ($business)
                <nav class="hidden items-center gap-1 lg:flex">
                    @foreach ($nav as [$route, $label, $pattern])
                        <a href="{{ route($route) }}" @class(['rounded-lg px-3 py-2 text-sm font-semibold transition', 'bg-daun-muda text-daun-tua' => request()->routeIs($pattern), 'text-abu hover:bg-santan hover:text-tinta' => ! request()->routeIs($pattern)])>{{ $label }}</a>
                    @endforeach
                </nav>
            @endif

            <div class="flex items-center gap-2">
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                        @csrf
                        <button class="rounded-lg px-3 py-2 text-sm font-semibold text-abu hover:text-terakota">Keluar</button>
                    </form>
                @endauth
                @if ($business)
                    <button type="button" class="rounded-lg border border-garis px-3 py-2 text-sm font-semibold lg:hidden" @click="buka = !buka" :aria-expanded="buka">Menu</button>
                @endif
            </div>
        </div>

        @if ($business)
            <nav class="border-t border-garis px-4 py-2 lg:hidden" x-show="buka" x-cloak>
                @foreach ($nav as [$route, $label, $pattern])
                    <a href="{{ route($route) }}" @class(['block rounded-lg px-3 py-2 text-sm font-semibold', 'bg-daun-muda text-daun-tua' => request()->routeIs($pattern)])>{{ $label }}</a>
                @endforeach
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="block w-full rounded-lg px-3 py-2 text-left text-sm font-semibold text-terakota">Keluar</button>
                </form>
            </nav>
        @endif
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6 sm:py-8">
        @if (session('status'))
            <div class="mb-5 rounded-xl border border-daun/30 bg-daun-muda px-4 py-3 text-sm font-medium text-daun-tua" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-terakota/30 bg-terakota-muda px-4 py-3 text-sm text-terakota" role="alert">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </main>

    <footer class="mx-auto max-w-6xl px-4 pb-10 text-xs text-abu">
        Pasok Bali membantu mengukur kepatuhan Pergub Bali No. 99/2018 secara mandiri. Angka kuota mengikuti pengaturan admin; cocokkan dengan teks resmi peraturan.
    </footer>
</body>
</html>
