<?php

namespace App\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiCompatibleDriver implements AiDriver
{
    public function __construct(private string $apiKey, private string $baseUrl, private string $model, private float $timeout) {}

    public function name(): string
    {
        return 'openai';
    }

    public function model(): string
    {
        return $this->model;
    }

    public function json(string $system, string $input, array $schema, int $maxTokens, array $images = []): ?array
    {
        $content = [['type' => 'text', 'text' => $input]];

        foreach ($images as $image) {
            $content[] = ['type' => 'image_url', 'image_url' => ['url' => 'data:'.$image['mime'].';base64,'.$image['data']]];
        }

        $response = Http::withToken($this->apiKey)
            ->acceptJson()
            ->timeout((int) ceil($this->timeout))
            ->connectTimeout(8)
            ->post(rtrim($this->baseUrl, '/').'/chat/completions', [
                'model' => $this->model,
                'max_tokens' => $maxTokens,
                'temperature' => 0,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => $system."\n\nBalas HANYA satu objek JSON yang sesuai skema berikut, tanpa teks lain:\n".json_encode($schema, JSON_UNESCAPED_UNICODE)],
                    ['role' => 'user', 'content' => $content],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('HTTP '.$response->status().': '.mb_substr((string) $response->json('error.message', $response->body()), 0, 200));
        }

        if ($response->json('choices.0.finish_reason') === 'content_filter') {
            throw new AiRefusedException('Permintaan ditolak model');
        }

        return self::decode((string) $response->json('choices.0.message.content'));
    }

    public static function decode(string $text): ?array
    {
        $text = trim($text);

        if (str_starts_with($text, '```')) {
            $text = trim((string) preg_replace('/^```[a-z]*\s*|\s*```$/i', '', $text));
        }

        $data = json_decode($text, true);

        return is_array($data) ? $data : null;
    }
}
