<?php

use App\Ai\OpenAiCompatibleDriver;
use Illuminate\Support\Facades\Http;

it('pindah ke model cadangan saat model utama sibuk atau ditutup', function () {
    Http::fakeSequence('generativelanguage.test/*')
        ->push([['error' => ['code' => 503, 'message' => 'high demand']]], 503)
        ->push([['error' => ['code' => 404, 'message' => 'no longer available']]], 404)
        ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => '{"ok":true}']]]]);

    $driver = new OpenAiCompatibleDriver('kunci-uji', 'https://generativelanguage.test/v1', 'model-a', 10, ['model-b', 'model-c']);

    expect($driver->json('sistem', 'masukan', ['type' => 'object'], 100))->toBe(['ok' => true])
        ->and($driver->model())->toBe('model-c');

    $models = collect(Http::recorded())->map(fn ($pair) => $pair[0]->data()['model'])->all();
    expect($models)->toBe(['model-a', 'model-b', 'model-c']);
});

it('memberi ruang token berpikir dan pindah model bila jawaban terpotong', function () {
    Http::fakeSequence('generativelanguage.test/*')
        ->push(['choices' => [['finish_reason' => 'length', 'message' => ['content' => '{"summ']]]])
        ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => '{"ok":true}']]]]);

    $driver = new OpenAiCompatibleDriver('kunci-uji', 'https://generativelanguage.test/v1', 'model-a', 10, ['model-b']);

    expect($driver->json('sistem', 'masukan', ['type' => 'object'], 600))->toBe(['ok' => true])
        ->and($driver->model())->toBe('model-b')
        ->and(Http::recorded()[0][0]->data()['max_tokens'])->toBeGreaterThan(2000);
});

it('tidak mencoba model lain untuk kesalahan permintaan', function () {
    Http::fake(['generativelanguage.test/*' => Http::response(['error' => ['message' => 'bad request']], 400)]);

    $driver = new OpenAiCompatibleDriver('kunci-uji', 'https://generativelanguage.test/v1', 'model-a', 10, ['model-b']);

    expect(fn () => $driver->json('sistem', 'masukan', ['type' => 'object'], 100))->toThrow(RuntimeException::class, 'HTTP 400 (model-a)');
    Http::assertSentCount(1);
});
