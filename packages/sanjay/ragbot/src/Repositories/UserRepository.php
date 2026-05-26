<?php

namespace Sanjay\Ragbot\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    /**
     * Search and filter users for a project with pagination.
     */
    public function searchForProject(
        string $projectId,
        ?string $search = null,
        ?string $status = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->model
            ->where('project_id', $projectId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('firstname', 'like', '%'.$search.'%')
                        ->orWhere('lastname', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                if ($status === 'verified') {
                    $query->whereNotNull('email_verified_at');
                } elseif ($status === 'pending') {
                    $query->whereNull('email_verified_at');
                }
            })
            ->latest()
            ->paginate($perPage);
    }
}
