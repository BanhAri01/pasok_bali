<?php

namespace Tests\Fakes;

use App\Ai\AiDriver;

class FakeAiDriver implements AiDriver
{
    public array $calls = [];

    public function __construct(private array $responses = []) {}

    public function name(): string
    {
        return 'palsu';
    }

    public function model(): string
    {
        return 'palsu-1';
    }

    public function json(string $system, string $input, array $schema, int $maxTokens, array $images = []): ?array
    {
        $kind = match (true) {
            in_array('legible', $schema['required'] ?? [], true) => 'nota',
            in_array('products', $schema['required'] ?? [], true) => 'daftar',
            in_array('summary', $schema['required'] ?? [], true) => 'ringkasan',
            in_array('message', $schema['required'] ?? [], true) => 'pesan',
            default => 'kelompok',
        };

        $this->calls[] = ['kind' => $kind, 'input' => $input, 'images' => count($images)];

        $response = $this->responses[$kind] ?? null;

        return is_callable($response) ? $response($input) : $response;
    }

    public function count(string $kind): int
    {
        return collect($this->calls)->where('kind', $kind)->count();
    }
}
