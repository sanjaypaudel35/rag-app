<?php

namespace Sanjay\Ragbot\Enums;

/**
 * Enum representing supported LLM providers.
 */
enum LlmProvider: string
{
    case OpenAI = 'openai';
    case Anthropic = 'anthropic';
    case Gemini = 'gemini';
    case Stub = 'stub';

    /**
     * Get the chat models available for this provider.
     *
     * @return array<string, string>
     */
    public function models(): array
    {
        return LlmModel::forProvider($this);
    }

    /**
     * Get the embedding models available for this provider.
     *
     * @return array<string, string>
     */
    public function embeddingModels(): array
    {
        return LlmModel::embeddingsForProvider($this);
    }
}
