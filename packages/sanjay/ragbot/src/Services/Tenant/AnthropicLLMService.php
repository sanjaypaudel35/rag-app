<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Facades\Http;
use Sanjay\Ragbot\DTOs\LlmResponse;
use Sanjay\Ragbot\Exceptions\LlmResponseException;

/**
 * Service for interacting with Anthropic's Messages API.
 */
class AnthropicLLMService extends BaseLlmService
{
    /**
     * Generate a completion for the given prompt.
     *
     * @param  array<string, mixed>  $options
     *
     * @throws LlmResponseException
     */
    public function complete(string $prompt, array $options = []): LlmResponse
    {
        $apiKey = $this->settings->llm_api_key;
        $baseUrl = $this->settings->llm_api_endpoint ?? 'https://api.anthropic.com';
        $model = $this->settings->llm_model ?? config('ragbot.llm.providers.anthropic.model');

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])
            ->baseUrl($baseUrl)
            ->post('/v1/messages', [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => $options['temperature'] ?? config('ragbot.llm.temperature'),
                'max_tokens' => $options['max_tokens'] ?? config('ragbot.llm.max_tokens'),
            ]);

        if ($response->failed()) {
            throw new LlmResponseException('Anthropic API error: '.$response->body());
        }

        return new LlmResponse(
            content: $response->json('content.0.text') ?? '',
            inputTokens: $response->json('usage.input_tokens') ?? 0,
            outputTokens: $response->json('usage.output_tokens') ?? 0,
            model: $model
        );
    }
}
