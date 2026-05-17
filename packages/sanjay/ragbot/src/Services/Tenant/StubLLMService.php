<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Sanjay\Ragbot\DTOs\LlmResponse;

/**
 * Stub service for testing LLM interactions.
 */
class StubLLMService extends BaseLlmService
{
    /**
     * Generate a completion for the given prompt.
     *
     * @param  array<string, mixed>  $options
     */
    public function complete(string $prompt, array $options = []): LlmResponse
    {
        return new LlmResponse(
            content: 'This is a stub response.',
            inputTokens: 10,
            outputTokens: 5,
            model: 'stub-model'
        );
    }
}
