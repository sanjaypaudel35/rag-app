<?php

namespace Sanjay\Ragbot\Services\Tenant;

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
    public function complete(string $prompt, array $options = []): string
    {
        return 'This is a stub response.';
    }
}
