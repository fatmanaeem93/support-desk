<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;


class ReplySuggestionService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = (string) config('services.anthropic.api_key');
        $this->model = (string) config('services.anthropic.model', 'claude-sonnet-4-5');
    }

    public function suggest(string $ticketText, string $category, string $priority): string
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('Anthropic API key غير مضبوط.');
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])
            ->timeout(20)
            ->retry(2, 500)
            ->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 300,
                'system' => "إنت موظف دعم فني محترف. اكتب رد مبدئي مهذب ومختصر (3-4 جمل) "
                    . "لتذكرة مصنفة كـ '{$category}' وأولويتها '{$priority}'. "
                    . 'الرد يكون بالعربي، متعاطف، ومحدد، من غير حلول تقنية مضمونة لو مش متأكد.',
                'messages' => [
                    ['role' => 'user', 'content' => $ticketText],
                ],
            ]);

        if ($response->failed()) {
            Log::error('ReplySuggestionService: AI request failed', ['body' => $response->body()]);
            throw new RuntimeException('تعذر توليد رد مقترح حاليًا.');
        }

        $text = collect($response->json('content', []))
            ->firstWhere('type', 'text')['text'] ?? null;

        if ($text === null) {
            throw new RuntimeException('لم يتم استلام رد من الذكاء الاصطناعي.');
        }

        return $text;
    }
}
