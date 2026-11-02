<?php

use App\Ai\AiGateway;
use App\Models\Business;
use App\Models\User;
use App\Support\CurrentBusiness;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeAiDriver;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        $this->seed(DatabaseSeeder::class);
        app(AiGateway::class)->useDriver(null);
        app(CurrentBusiness::class)->set(null);
    })
    ->in('Feature');

function owner(array $business = []): User
{
    $model = Business::query()->create($business + ['name' => 'Warung Uji', 'type' => 'restoran', 'regency' => 'Badung']);

    return User::query()->create([
        'business_id' => $model->id,
        'name' => 'Pemilik '.$model->id,
        'email' => 'pemilik'.$model->id.'@contoh.test',
        'password' => 'rahasia-panjang',
        'role' => User::OWNER,
    ]);
}

function fakeAi(array $responses = []): FakeAiDriver
{
    $driver = new FakeAiDriver($responses);
    app(AiGateway::class)->useDriver($driver);

    return $driver;
}

function inBusiness(?int $businessId): void
{
    app(CurrentBusiness::class)->set($businessId);
}
