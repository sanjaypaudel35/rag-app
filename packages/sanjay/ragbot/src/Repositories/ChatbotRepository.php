<?php

namespace Sanjay\Ragbot\Repositories;

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
}
