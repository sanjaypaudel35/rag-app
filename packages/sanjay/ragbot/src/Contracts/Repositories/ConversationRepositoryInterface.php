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
     */
    public function findBySession(string $sessionId): ?Model;

    /**
     * Find or create a conversation by chatbot and session.
     */
    public function findOrCreate(string $chatbotId, string $sessionId, array $metadata = []): Model;
}
