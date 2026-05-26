<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Facades\Http;
use Sanjay\Ragbot\DTOs\LlmResponse;
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
    public function complete(string $prompt, array $options = []): LlmResponse
    {
        $apiKey = $this->settings->llm_api_key;
        $baseUrl = $this->settings->llm_api_endpoint ?? config('ragbot.llm.providers.openai.base_url');
        $model = $this->settings->llm_model ?? config('ragbot.llm.providers.openai.model');

        $response = Http::withToken($apiKey)
            ->baseUrl($baseUrl)
            ->post('/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => $options['temperature'] ?? config('ragbot.llm.temperature'),
                'max_tokens' => $options['max_tokens'] ?? config('ragbot.llm.max_tokens'),
            ]);

        if ($response->failed()) {
            throw new LlmResponseException('OpenAI API error: '.$response->body());
        }

        return new LlmResponse(
            content: $response->json('choices.0.message.content') ?? '',
            inputTokens: $response->json('usage.prompt_tokens') ?? 0,
            outputTokens: $response->json('usage.completion_tokens') ?? 0,
            model: $model
        );
    }
}
