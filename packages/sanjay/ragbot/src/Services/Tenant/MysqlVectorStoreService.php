<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Collection;
use Sanjay\Ragbot\Contracts\Services\VectorStoreInterface;
use Sanjay\Ragbot\Models\Embedding;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Support\VectorHelper;

class MysqlVectorStoreService implements VectorStoreInterface
{
    /**
     * Stores and retrieves vector embeddings.
     *
     * @param  array<float>  $vector
     */
    public function store(Project $project, string $chunkId, array $vector): void
    {
        Embedding::updateOrCreate(
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
     */
    public function search(Project $project, array $queryVector, int $topK = 5): Collection
    {
        return Embedding::where('project_id', $project->id)
            ->with('chunk')
            ->get()
            ->map(function ($embedding) use ($queryVector) {
                $embedding->similarity = VectorHelper::cosineSimilarity($queryVector, $embedding->vector);

                return $embedding;
            })
            ->sortByDesc('similarity')
            ->take($topK)
            ->pluck('chunk');
    }
}
