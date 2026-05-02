<?php

namespace Sanjay\Ragbot\Repositories;

use Illuminate\Database\Eloquent\Model;
use Sanjay\Ragbot\Contracts\Repositories\UserRepositoryInterface;
use Sanjay\Ragbot\Models\RagbotUser;

/**
 * Repository for managing RagbotUser entities.
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Create a new user repository instance.
     */
    public function __construct(RagbotUser $model)
    {
        parent::__construct($model);
    }

    /**
     * Find a user by email scoped by project.
     */
    public function findByEmail(string $projectId, string $email): ?Model
    {
        return $this->model
            ->withoutGlobalScope('project')
            ->where('project_id', $projectId)
            ->where('email', $email)
            ->first();
    }

    /**
     * Create a new user for a specific project.
     *
     * @param  array<string, mixed>  $data
     */
    public function createForProject(string $projectId, array $data): Model
    {
        $data['project_id'] = $projectId;

        return $this->model
            ->withoutGlobalScope('project')
            ->create($data);
    }
}
