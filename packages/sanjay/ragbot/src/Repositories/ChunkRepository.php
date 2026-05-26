<?php

namespace Sanjay\Ragbot\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Contracts\Repositories\ChunkRepositoryInterface;
use Sanjay\Ragbot\Models\Chunk;

/**
 * Repository for managing Chunk entities.
 */
class ChunkRepository extends BaseRepository implements ChunkRepositoryInterface
{
    /**
     * Create a new chunk repository instance.
     */
    public function __construct(Chunk $model)
    {
        parent::__construct($model);
    }

    /**
     * Find chunks by document.
     *
     * @return Collection<int, Chunk>
     */
    public function findByDocument(string $documentId): Collection
    {
        return $this->model
            ->where('document_id', $documentId)
            ->get();
    }

    /**
     * Search for chunks using full-text search.
     *
     * @param  array<string>  $documentIds
     */
    public function searchKeyword(string $projectId, string $query, array $documentIds = [], int $limit = 5): Collection
    {
        $dbQuery = $this->model->where('project_id', $projectId);

        if (! empty($documentIds)) {
            $dbQuery->whereIn('document_id', $documentIds);
        }

        $driver = $this->model->getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL full-text search using plainto_tsquery for better multi-word handling
            return $dbQuery->whereRaw('to_tsvector(\'english\', content) @@ plainto_tsquery(\'english\', ?)', [$query])
                ->orderByRaw('ts_rank(to_tsvector(\'english\', content), plainto_tsquery(\'english\', ?)) DESC', [$query])
                ->take($limit)
                ->get();
        }

        if ($driver === 'mysql') {
            return $dbQuery->whereFullText('content', $query)
                ->take($limit)
                ->get();
        }

        // Fallback for other drivers (simple LIKE)
        return $dbQuery->where('content', 'LIKE', "%{$query}%")
            ->take($limit)
            ->get();
    }
}
