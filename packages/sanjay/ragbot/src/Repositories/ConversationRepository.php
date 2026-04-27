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
     *
     * @param Conversation $model
     */
    public function __construct(Conversation $model)
    {
        parent::__construct($model);
    }

    /**
     * Find conversation by session scoped by project.
     *
     * @param string $projectId
     * @param string $sessionId
     * @return Conversation|null
     */
    public function findBySession(string $projectId, string $sessionId): ?Conversation
    {
        return $this->model
            ->where("project_id", $projectId)
            ->where("session_id", $sessionId)
            ->first();
    }
}
