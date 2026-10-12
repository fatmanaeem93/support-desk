<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;


class TicketClassifierService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = (string) config('services.ml_classifier.url', 'http://127.0.0.1:8001');
    }

    /**
     * @return array{category:string, priority:string, category_confidence:float, priority_confidence:float}
     */
    public function classify(string $text): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/classify", [
                'text' => $text,
            ]);
        } catch (\Throwable $e) {
            Log::error('TicketClassifierService: connection failed', ['message' => $e->getMessage()]);
            throw new RuntimeException('خدمة التصنيف مش شغالة حاليًا. تأكدي إن ml_service شغال.');
        }

        if ($response->failed()) {
            Log::error('TicketClassifierService: request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('تعذر تصنيف التذكرة حاليًا.');
        }

        return $response->json();
    }
}
