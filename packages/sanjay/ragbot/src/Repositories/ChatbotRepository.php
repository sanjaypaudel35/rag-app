<?php

namespace Sanjay\Ragbot\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Contracts\Repositories\ChatbotRepositoryInterface;
use Sanjay\Ragbot\Models\Chatbot;

/**
 * Repository for managing Chatbot entities.
 */
class ChatbotRepository extends BaseRepository implements ChatbotRepositoryInterface
{
    /**
     * Create a new chatbot repository instance.
     */
    public function __construct(Chatbot $model)
    {
        parent::__construct($model);
    }

    /**
     * Find a chatbot by its unique API key.
     */
    public function findByApiKey(string $apiKey): ?Chatbot
    {
        return $this->model
            ->where('api_key', hash('sha256', $apiKey))
            ->first();
    }

    /**
     * Get chatbots for a project with optional relationships.
     *
     * @param  array<string>  $relationships
     */
    public function getForProject(string $projectId, array $relationships = []): Collection
    {
        return $this->model
            ->where('project_id', $projectId)
            ->with($relationships)
            ->latest()
            ->get();
    }

    /**
     * Increment a column value for a chatbot.
     */
    public function increment(string $chatbotId, string $column, int $amount = 1): void
    {
        $this->model->where('id', $chatbotId)->increment($column, $amount);
    }

    /**
     * Update model usage statistics for a chatbot.
     */
    public function updateModelUsage(
        string $chatbotId,
        string $model,
        int $inputTokens,
        int $outputTokens,
        float $cost
    ): void {
        $chatbot = $this->findById($chatbotId);

        $usage = $chatbot->modelUsage()->firstOrCreate(
            ['model' => $model],
            ['project_id' => $chatbot->project_id]
        );

        $usage->increment('input_tokens', $inputTokens);
        $usage->increment('output_tokens', $outputTokens);
        $usage->increment('cost', $cost);
    }
}
