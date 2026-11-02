<?php

namespace App\Ai;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiCompatibleDriver implements AiDriver
{
    private const RETRYABLE = [404, 408, 429, 500, 502, 503, 504];

    private string $used;

    public function __construct(private string $apiKey, private string $baseUrl, private string $model, private float $timeout, private array $fallbacks = [])
    {
        $this->used = $model;
    }

    public function name(): string
    {
        return 'openai';
    }

    public function model(): string
    {
        return $this->used;
    }

    public function json(string $system, string $input, array $schema, int $maxTokens, array $images = []): ?array
    {
        $content = [['type' => 'text', 'text' => $input]];

        foreach ($images as $image) {
            $content[] = ['type' => 'image_url', 'image_url' => ['url' => 'data:'.$image['mime'].';base64,'.$image['data']]];
        }

        $messages = [
            ['role' => 'system', 'content' => $system."\n\nBalas HANYA satu objek JSON yang sesuai skema berikut, tanpa teks lain:\n".json_encode($schema, JSON_UNESCAPED_UNICODE)],
            ['role' => 'user', 'content' => $content],
        ];

        $models = array_values(array_unique(array_filter([$this->model, ...$this->fallbacks])));
        $response = null;
        $error = null;

        foreach ($models as $model) {
            $this->used = $model;

            try {
                $response = $this->send($model, $messages, $maxTokens);
            } catch (ConnectionException $exception) {
                [$response, $error] = [null, $exception->getMessage()];

                continue;
            }

            if ($response->successful() || ! in_array($response->status(), self::RETRYABLE, true)) {
                break;
            }

            $error = $this->errorMessage($response);
        }

        if ($response === null) {
            throw new RuntimeException('Koneksi gagal: '.mb_substr((string) $error, 0, 200));
        }

        if ($response->failed()) {
            throw new RuntimeException($this->errorMessage($response));
        }

        if ($response->json('choices.0.finish_reason') === 'content_filter') {
            throw new AiRefusedException('Permintaan ditolak model');
        }

        return self::decode((string) $response->json('choices.0.message.content'));
    }

    private function send(string $model, array $messages, int $maxTokens): Response
    {
        return Http::withToken($this->apiKey)
            ->acceptJson()
            ->timeout((int) ceil($this->timeout))
            ->connectTimeout(8)
            ->post(rtrim($this->baseUrl, '/').'/chat/completions', [
                'model' => $model,
                'max_tokens' => $maxTokens,
                'temperature' => 0,
                'response_format' => ['type' => 'json_object'],
                'messages' => $messages,
            ]);
    }

    private function errorMessage(Response $response): string
    {
        $body = $response->json();
        $message = $body['error']['message'] ?? $body[0]['error']['message'] ?? $response->body();

        return 'HTTP '.$response->status().' ('.$this->used.'): '.mb_substr((string) $message, 0, 200);
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
