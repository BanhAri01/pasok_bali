@props(['month', 'route'])

<form method="GET" action="{{ route($route) }}" class="flex items-center gap-2">
    <label for="bulan" class="text-sm text-abu">Bulan</label>
    <input id="bulan" type="month" name="bulan" value="{{ $month->format('Y-m') }}" max="{{ now()->format('Y-m') }}" class="rounded-lg border border-garis bg-kertas px-3 py-2 text-sm" onchange="this.form.submit()">
</form>
