<?php

namespace Database\Seeders;

use App\Models\QuotaRule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([ProductSeeder::class, DemoSupplierSeeder::class]);

        foreach (config('pasok.quota_groups') as $group => $rule) {
            QuotaRule::query()->firstOrCreate(
                ['group' => $group],
                ['min_percent' => $rule['percent'], 'source' => 'Pergub Bali No. 99 Tahun 2018 (cocokkan dengan teks resmi JDIH Pemprov Bali)'],
            );
        }

        $email = (string) env('PASOK_ADMIN_EMAIL', '');

        if ($email !== '' && ! User::query()->where('email', $email)->exists()) {
            $password = Str::password(16, symbols: false);

            User::query()->create([
                'name' => 'Admin Pasok Bali',
                'email' => $email,
                'password' => $password,
                'role' => User::ADMIN,
            ]);

            $this->command?->warn("Akun admin {$email} dibuat. Sandi awal: {$password} (segera ganti).");
        }
    }
}
