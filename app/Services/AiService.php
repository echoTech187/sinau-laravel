<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', env('GEMINI_API_KEY', ''));
    }

    /**
     * Generate content using Gemini AI.
     */
    public function generate(string $prompt): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('Gemini API key is not set.');
            return null;
        }

        try {
            $response = Http::post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 1,
                    'topP' => 1,
                    'maxOutputTokens' => 1000,
                ]
            ]);

            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text');
            }

            Log::error('Gemini API Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Gemini AI Exception: ' . $e->getMessage());
            return null;
        }
    }
}
