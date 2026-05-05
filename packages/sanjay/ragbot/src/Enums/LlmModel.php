<?php

namespace Sanjay\Ragbot\Enums;

/**
 * Enum representing common LLM models for different providers.
 */
enum LlmModel: string
{
    case Gpt4o = 'gpt-4o';
    case Gpt4oMini = 'gpt-4o-mini';
    case Gpt35Turbo = 'gpt-3.5-turbo';

    case Claude35Sonnet = 'claude-3-5-sonnet-20240620';
    case Claude3Opus = 'claude-3-opus-20240229';
    case Claude3Haiku = 'claude-3-haiku-20240307';

    case TextEmbedding3Small = 'text-embedding-3-small';
    case TextEmbedding3Large = 'text-embedding-3-large';
    case TextEmbeddingAda002 = 'text-embedding-ada-002';

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
                self::Gpt35Turbo->value => 'GPT-3.5 Turbo',
            ],
            LlmProvider::Anthropic => [
                self::Claude35Sonnet->value => 'Claude 3.5 Sonnet',
                self::Claude3Opus->value => 'Claude 3 Opus',
                self::Claude3Haiku->value => 'Claude 3 Haiku',
            ],
            default => [],
        };
    }

    /**
     * Get embedding models for a specific provider.
     *
     * @return array<string, string>
     */
    public static function embeddingsForProvider(LlmProvider $provider): array
    {
        return match ($provider) {
            LlmProvider::OpenAI => [
                self::TextEmbedding3Small->value => 'Text Embedding 3 Small',
                self::TextEmbedding3Large->value => 'Text Embedding 3 Large',
                self::TextEmbeddingAda002->value => 'Text Embedding Ada 002',
            ],
            default => [],
        };
    }
}
