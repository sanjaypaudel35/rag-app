<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Facades\Http;
use Sanjay\Ragbot\Exceptions\LlmResponseException;

/**
 * Service for interacting with OpenAI's Chat Completions API.
 */
class OpenAILLMService extends BaseLlmService
{
    /**
     * Generate a completion for the given prompt.
     *
     * @param  array<string, mixed>  $options
     *
     * @throws LlmResponseException
     */
    public function complete(string $prompt, array $options = []): string
    {
        $apiKey = $this->settings->llm_api_key;
        $baseUrl = $this->settings->llm_api_endpoint ?? 'https://api.openai.com/v1';
        $model = $this->settings->llm_model ?? config('ragbot.llm.providers.openai.model');

        $response = Http::withToken($apiKey)
            ->baseUrl($baseUrl)
            ->post('/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => $options['temperature'] ?? 0.7,
                'max_tokens' => $options['max_tokens'] ?? 1000,
            ]);

        if ($response->failed()) {
            throw new LlmResponseException('OpenAI API error: '.$response->body());
        }

        return $response->json('choices.0.message.content') ?? '';
    }
}
