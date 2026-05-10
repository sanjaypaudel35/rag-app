<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Message repository.
 */
interface MessageRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find messages by conversation.
     */
    public function findByConversation(string $conversationId): Collection;
}
