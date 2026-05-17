<?php

namespace Sanjay\Ragbot\DTOs;

/**
 * Data Transfer Object for LLM responses.
 */
class LlmResponse
{
    /**
     * Create a new LlmResponse instance.
     */
    public function __construct(
        public string $content,
        public int $inputTokens = 0,
        public int $outputTokens = 0,
        public ?string $model = null
    ) {}

    /**
     * Get the total tokens used.
     */
    public function totalTokens(): int
    {
        return $this->inputTokens + $this->outputTokens;
    }
}
