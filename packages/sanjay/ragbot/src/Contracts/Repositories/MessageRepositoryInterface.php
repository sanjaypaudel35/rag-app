<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Models\Message;

/**
 * Interface for Message repository.
 */
interface MessageRepositoryInterface
{
    /**
     * Find a message by its unique identifier scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return Message|null
     */
    public function findById(string $id, string $projectId): ?Message;

    /**
     * Create a new message in the database.
     *
     * @param array<string, mixed> $data
     * @return Message
     */
    public function create(array $data): Message;

    /**
     * Update an existing message scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(string $id, string $projectId, array $data): bool;

    /**
     * Delete a message from the database scoped by project.
     *
     * @param string $id
     * @param string $projectId
     * @return bool
     */
    public function delete(string $id, string $projectId): bool;

    /**
     * Get all messages scoped by project_id.
     *
     * @param string $projectId
     * @return Collection<int, Message>
     */
    public function allForProject(string $projectId): Collection;

    /**
     * Find messages by conversation scoped by project.
     *
     * @param string $projectId
     * @param string $conversationId
     * @return Collection<int, Message>
     */
    public function findByConversation(string $projectId, string $conversationId): Collection;
}
