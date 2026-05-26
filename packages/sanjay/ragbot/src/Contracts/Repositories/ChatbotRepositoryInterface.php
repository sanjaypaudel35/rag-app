<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface for Chatbot repository.
 */
interface ChatbotRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a chatbot by its unique API key.
     */
    public function findByApiKey(string $apiKey): ?Model;

    /**
     * Get chatbots for a project with optional relationships.
     *
     * @param  array<string>  $relationships
     */
    public function getForProject(string $projectId, array $relationships = []): Collection;

    /**
     * Increment a column value for a chatbot.
     */
    public function increment(string $chatbotId, string $column, int $amount = 1): void;

    /**
     * Update model usage statistics for a chatbot.
     */
    public function updateModelUsage(
        string $chatbotId,
        string $model,
        int $inputTokens,
        int $outputTokens,
        float $cost
    ): void;
}
