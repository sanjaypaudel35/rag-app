<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Sanjay\Ragbot\Contracts\Repositories\ChunkRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\EmbeddingRepositoryInterface;
use Sanjay\Ragbot\Contracts\Services\KeywordSearchInterface;
use Sanjay\Ragbot\Contracts\Services\VectorStoreInterface;
use Sanjay\Ragbot\Models\Project;

class PgVectorStoreService implements KeywordSearchInterface, VectorStoreInterface
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        protected EmbeddingRepositoryInterface $embeddingRepository,
        protected ChunkRepositoryInterface $chunkRepository
    ) {}

    /**
     * Stores vector embeddings in PostgreSQL.
     *
     * @param  array<float>  $vector
     */
    public function store(Project $project, string $chunkId, array $vector): void
    {
        // For pgvector, we can pass the array directly if using a compatible driver,
        // or format it as a string for raw queries.
        // Eloquent with array cast might work depending on the DB driver setup.

        $this->embeddingRepository->updateOrCreate(
            [
                'project_id' => $project->id,
                'chunk_id' => $chunkId,
            ],
            [
                'vector' => $vector,
            ]
        );
    }

    /**
     * Searches for chunks similar to the query vector using PGVector's cosine distance operator.
     *
     * @param  array<float>  $queryVector
     * @param  array<string>  $documentIds
     */
    public function search(Project $project, array $queryVector, int $topK = 5, array $documentIds = []): Collection
    {
        $vectorString = '['.implode(',', $queryVector).']';

        return $this->embeddingRepository->searchWithChunks(
            $project->id,
            $documentIds,
            'vector <=> ?::vector',
            [$vectorString],
            $topK
        )->pluck('chunk');
    }

    /**
     * Searches for chunks matching the query keywords.
     *
     * @param  array<string>  $documentIds
     */
    public function searchKeyword(Project $project, string $query, int $topK = 5, array $documentIds = []): Collection
    {
        return $this->chunkRepository->searchKeyword($project->id, $query, $documentIds, $topK);
    }
}
