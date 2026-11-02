<?php

namespace App\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class AnthropicDriver implements AiDriver
{
    public function __construct(private string $apiKey, private string $model, private float $timeout) {}

    public function name(): string
    {
        return 'anthropic';
    }

    public function model(): string
    {
        return $this->model;
    }

    public function json(string $system, string $input, array $schema, int $maxTokens, array $images = []): ?array
    {
        $content = [];

        foreach ($images as $image) {
            $content[] = ['type' => 'image', 'source' => ['type' => 'base64', 'media_type' => $image['mime'], 'data' => $image['data']]];
        }

        $content[] = ['type' => 'text', 'text' => $input];

        $response = Http::withHeaders(['x-api-key' => $this->apiKey, 'anthropic-version' => '2023-06-01'])
            ->acceptJson()
            ->timeout((int) ceil($this->timeout))
            ->connectTimeout(8)
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model,
                'max_tokens' => $maxTokens,
                'system' => $system,
                'messages' => [['role' => 'user', 'content' => $content]],
                'output_config' => ['format' => ['type' => 'json_schema', 'schema' => $schema]],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('HTTP '.$response->status().': '.mb_substr((string) $response->json('error.message', $response->body()), 0, 200));
        }

        if ($response->json('stop_reason') === 'refusal') {
            throw new AiRefusedException('Permintaan ditolak model');
        }

        $text = collect($response->json('content', []))->where('type', 'text')->pluck('text')->implode('');

        return OpenAiCompatibleDriver::decode($text);
    }
}
