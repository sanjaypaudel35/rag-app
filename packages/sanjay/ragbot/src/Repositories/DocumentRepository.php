<?php

namespace Sanjay\Ragbot\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Models\Document;

/**
 * Repository for managing Document entities.
 */
class DocumentRepository extends BaseRepository implements DocumentRepositoryInterface
{
    /**
     * Create a new document repository instance.
     */
    public function __construct(Document $model)
    {
        parent::__construct($model);
    }

    /**
     * Find documents by status.
     *
     * @return Collection<int, Document>
     */
    public function findByStatus(string $status): Collection
    {
        return $this->model
            ->where('status', $status)
            ->get();
    }

    /**
     * Update document status.
     */
    public function updateStatus(string $id, string $status, ?string $error = null): bool
    {
        $data = ['status' => $status];

        if ($error !== null) {
            $data['error_message'] = $error;
        }

        $this->update($id, $data);

        return true;
    }

    /**
     * Get documents for a project with chunk counts.
     */
    public function getForProject(string $projectId): Collection
    {
        return $this->model
            ->where('project_id', $projectId)
            ->withCount('chunks')
            ->latest()
            ->get();
    }

    /**
     * Get document statistics for a project.
     *
     * @return array<string, int>
     */
    public function getStats(string $projectId): array
    {
        $baseQuery = $this->model->where('project_id', $projectId);

        return [
            'total' => (clone $baseQuery)->count(),
            'processing' => (clone $baseQuery)->where('status', DocumentStatus::Processing)->count(),
            'completed' => (clone $baseQuery)->where('status', DocumentStatus::Completed)->count(),
            'failed' => (clone $baseQuery)->where('status', DocumentStatus::Failed)->count(),
        ];
    }

    /**
     * Search and filter documents for a project with pagination.
     */
    public function searchForProject(
        string $projectId,
        ?string $search = null,
        ?string $statusFilter = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->model
            ->where('project_id', $projectId)
            ->withCount('chunks')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->when($statusFilter, function ($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get recent documents for a project with limited statuses.
     *
     * @param  array<string>  $statuses
     */
    public function getRecentForProject(string $projectId, array $statuses = [], int $limit = 10): Collection
    {
        return $this->model
            ->where('project_id', $projectId)
            ->when(! empty($statuses), function ($query) use ($statuses) {
                $query->whereIn('status', $statuses);
            })
            ->latest()
            ->take($limit)
            ->get();
    }
}
