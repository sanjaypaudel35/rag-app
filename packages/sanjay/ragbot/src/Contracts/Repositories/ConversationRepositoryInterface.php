<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface for Conversation repository.
 */
interface ConversationRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find conversation by session.
     *
     * @param string $sessionId
     * @return Model|null
     */
    public function findBySession(string $sessionId): ?Model;
}
