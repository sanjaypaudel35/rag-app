<?php

namespace Sanjay\Ragbot\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Contracts\Repositories\MessageRepositoryInterface;
use Sanjay\Ragbot\Models\Message;

/**
 * Repository for managing Message entities.
 */
class MessageRepository extends BaseRepository implements MessageRepositoryInterface
{
    /**
     * Create a new message repository instance.
     *
     * @param Message $model
     */
    public function __construct(Message $model)
    {
        parent::__construct($model);
    }

    /**
     * Find messages by conversation scoped by project.
     *
     * @param string $projectId
     * @param string $conversationId
     * @return Collection<int, Message>
     */
    public function findByConversation(string $projectId, string $conversationId): Collection
    {
        return $this->model
            ->where("project_id", $projectId)
            ->where("conversation_id", $conversationId)
            ->get();
    }
}
