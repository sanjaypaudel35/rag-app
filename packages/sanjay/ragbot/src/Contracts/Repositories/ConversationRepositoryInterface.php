<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Models\Conversation;

/**
 * Interface for Conversation repository.
 */
interface ConversationRepositoryInterface
{
    /**
     * Find a conversation by its unique identifier scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return Conversation|null
     */
    public function findById(string $id, string $projectId): ?Conversation;

    /**
     * Create a new conversation in the database.
     *
     * @param array<string, mixed> $data
     * @return Conversation
     */
    public function create(array $data): Conversation;

    /**
     * Update an existing conversation scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(string $id, string $projectId, array $data): bool;

    /**
     * Delete a conversation from the database scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return bool
     */
    public function delete(string $id, string $projectId): bool;

    /**
     * Get all conversations scoped by project_id.
     *
     * @param string $projectId
     * @return Collection<int, Conversation>
     */
    public function allForProject(string $projectId): Collection;

    /**
     * Find conversation by session scoped by project.
     *
     * @param string $projectId
     * @param string $sessionId
     * @return Conversation|null
     */
    public function findBySession(string $projectId, string $sessionId): ?Conversation;
}
