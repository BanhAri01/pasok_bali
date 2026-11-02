<?php

use App\Models\Business;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

it('membuat akun demo terpisah berisi data contoh untuk tiap pengunjung', function () {
    $this->post(route('demo'))->assertRedirect(route('dashboard'));
    $first = auth()->user()->business_id;

    $this->post(route('demo'));
    $second = auth()->user()->business_id;

    expect($first)->not->toBe($second)
        ->and(Business::query()->where('is_demo', true)->count())->toBe(2)
        ->and(Receipt::query()->where('business_id', $second)->where('status', Receipt::APPROVED)->count())->toBeGreaterThan(10);

    $this->get(route('dashboard'))->assertOk()->assertSee('Akun demo berisi data contoh fiktif')->assertSee('Peternakan');
});

it('menghapus akun demo kedaluwarsa dan mengeluarkan penggunanya', function () {
    $this->post(route('demo'));
    $business = Business::query()->where('is_demo', true)->sole();
    $business->update(['demo_expires_at' => now()->subMinute()]);

    $this->get(route('dashboard'))->assertRedirect(route('login'));

    Artisan::call('pasok:rapikan');

    expect(Business::query()->count())->toBe(0)
        ->and(User::query()->count())->toBe(0)
        ->and(Receipt::query()->count())->toBe(0);
});

it('membatasi percobaan masuk yang salah', function () {
    $user = owner();

    foreach (range(1, 5) as $attempt) {
        $this->post(route('login'), ['email' => $user->email, 'password' => 'salah'])->assertSessionHasErrors('email');
    }

    $this->post(route('login'), ['email' => $user->email, 'password' => 'rahasia-panjang'])->assertSessionHasErrors('email');
    expect(auth()->check())->toBeFalse();
});

it('mendaftarkan usaha baru', function () {
    $this->post(route('register'), [
        'business_name' => 'Kafe Sawah',
        'business_type' => 'kafe',
        'regency' => 'Gianyar',
        'name' => 'Komang',
        'email' => 'komang@contoh.test',
        'password' => 'kata-sandi-kuat',
        'password_confirmation' => 'kata-sandi-kuat',
    ])->assertRedirect(route('dashboard'));

    expect(auth()->user()->business->name)->toBe('Kafe Sawah');
});
