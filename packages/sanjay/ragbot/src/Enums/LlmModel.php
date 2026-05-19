<?php

namespace Sanjay\Ragbot\Enums;

/**
 * Enum representing common LLM models for different providers.
 */
enum LlmModel: string
{
    case Gpt4o = 'gpt-4o';
    case Gpt4oMini = 'gpt-4o-mini';

    case Claude35Sonnet = 'claude-3-5-sonnet-20240620';
    case Claude3Haiku = 'claude-3-haiku-20240307';

    case TextEmbedding3Small = 'text-embedding-3-small';
    case TextEmbedding3Large = 'text-embedding-3-large';
    case TextEmbeddingAda002 = 'text-embedding-ada-002';

    case StubModel = 'stub-model';
    case StubEmbeddingModel = 'stub-embedding-model';

    /**
     * Get models for a specific provider.
     *
     * @return array<string, string>
     */
    public static function forProvider(LlmProvider $provider): array
    {
        return match ($provider) {
            LlmProvider::OpenAI => [
                self::Gpt4o->value => 'GPT-4o',
                self::Gpt4oMini->value => 'GPT-4o Mini',
            ],
            LlmProvider::Anthropic => [
                self::Claude35Sonnet->value => 'Claude 3.5 Sonnet',
                self::Claude3Haiku->value => 'Claude 3 Haiku',
            ],
            LlmProvider::Stub => [
                self::StubModel->value => 'Stub Model',
            ],
        };
    }

    /**
     * Get embedding models for a specific provider.
     *
     * @return array<string, string>
     */
    public static function embeddingsForProvider(LlmProvider $provider): array
    {
        if ($provider === LlmProvider::Stub) {
            return [self::StubEmbeddingModel->value => 'Stub Embedding Model'];
        }

        // Restrict to OpenAI embedding models for all other providers
        return [
            self::TextEmbedding3Small->value => 'Text Embedding 3 Small',
            self::TextEmbedding3Large->value => 'Text Embedding 3 Large',
            self::TextEmbeddingAda002->value => 'Text Embedding Ada 002',
        ];
    }
}
