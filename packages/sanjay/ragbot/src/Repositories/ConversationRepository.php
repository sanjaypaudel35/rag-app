<?php

namespace Sanjay\Ragbot\Repositories;

use Sanjay\Ragbot\Contracts\Repositories\ConversationRepositoryInterface;
use Sanjay\Ragbot\Models\Conversation;

/**
 * Repository for managing Conversation entities.
 */
class ConversationRepository extends BaseRepository implements ConversationRepositoryInterface
{
    /**
     * Create a new conversation repository instance.
     */
    public function __construct(Conversation $model)
    {
        parent::__construct($model);
    }

    /**
     * Find conversation by session.
     */
    public function findBySession(string $sessionId): ?Conversation
    {
        return $this->model
            ->where('session_id', $sessionId)
            ->first();
    }

    /**
     * Find or create a conversation by chatbot and session.
     */
    public function findOrCreate(string $chatbotId, string $sessionId, array $metadata = []): Conversation
    {
        return $this->model->firstOrCreate(
            [
                'chatbot_id' => $chatbotId,
                'session_id' => $sessionId,
            ],
            [
                'project_id' => app('ragbot.project')->id,
                'metadata' => $metadata,
            ]
        );
    }
}
