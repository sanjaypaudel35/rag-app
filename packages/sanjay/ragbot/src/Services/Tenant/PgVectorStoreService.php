<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Sanjay\Ragbot\Contracts\Services\VectorStoreInterface;
use Sanjay\Ragbot\Models\Embedding;
use Sanjay\Ragbot\Models\Project;

class PgVectorStoreService implements VectorStoreInterface
{
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
     * Searches for chunks similar to the query vector using PGVector's cosine distance operator.
     *
     * @param  array<float>  $queryVector
     */
    public function search(Project $project, array $queryVector, int $topK = 5): Collection
    {
        $vectorString = '['.implode(',', $queryVector).']';

        return Embedding::where('project_id', $project->id)
            ->with('chunk')
            ->orderByRaw('vector <=> ?::vector', [$vectorString])
            ->take($topK)
            ->get()
            ->pluck('chunk');
    }
}
