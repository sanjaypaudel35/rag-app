<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Collection;
use Sanjay\Ragbot\Contracts\Repositories\ChunkRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\EmbeddingRepositoryInterface;
use Sanjay\Ragbot\Contracts\Services\KeywordSearchInterface;
use Sanjay\Ragbot\Contracts\Services\VectorStoreInterface;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Support\VectorHelper;

class MysqlVectorStoreService implements KeywordSearchInterface, VectorStoreInterface
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        protected EmbeddingRepositoryInterface $embeddingRepository,
        protected ChunkRepositoryInterface $chunkRepository
    ) {}

    /**
     * Stores and retrieves vector embeddings.
     *
     * @param  array<float>  $vector
     */
    public function store(Project $project, string $chunkId, array $vector): void
    {
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
     * Searches for chunks similar to the query vector using PHP-side cosine similarity.
     *
     * @param  array<float>  $queryVector
     * @param  array<string>  $documentIds
     */
    public function search(Project $project, array $queryVector, int $topK = 5, array $documentIds = []): Collection
    {
        return $this->embeddingRepository->getWithChunks($project->id, $documentIds)
            ->map(function ($embedding) use ($queryVector) {
                $embedding->similarity = VectorHelper::cosineSimilarity($queryVector, $embedding->vector);

                return $embedding;
            })
            ->sortByDesc('similarity')
            ->take($topK)
            ->pluck('chunk');
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
