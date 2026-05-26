<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Collection;
use Sanjay\Ragbot\Contracts\Services\RetrievalServiceInterface;
use Sanjay\Ragbot\Models\Chatbot;
use Sanjay\Ragbot\Models\Project;

/**
 * Service for retrieving relevant document chunks for a chatbot.
 */
class RetrievalService implements RetrievalServiceInterface
{
    /**
     * The constant for RRF calculation.
     */
    protected const RRF_K = 60;

    /**
     * Create a new retrieval service instance.
     */
    public function __construct(
        protected EmbeddingManager $embeddingManager,
        protected VectorStoreManager $vectorStoreManager
    ) {}

    /**
     * Retrieves the most relevant chunks for a given query using a hybrid approach (Vector + BM25).
     */
    public function retrieve(Project $project, string $query, int $topK = 5): Collection
    {
        // 1. Resolve document IDs filter
        $documentIds = $this->resolveDocumentIds();

        $vectorLimit = config('ragbot.retrieval.hybrid.vector_limit', 20);
        $keywordLimit = config('ragbot.retrieval.hybrid.keyword_limit', 20);

        // 2. Perform Vector Search
        $queryVector = $this->embeddingManager->embed($query, $project);
        $vectorResults = $this->vectorStoreManager->search($project, $queryVector, $vectorLimit, $documentIds);

        // 3. Perform Keyword Search (BM25)
        $keywordResults = $this->vectorStoreManager->searchKeyword($project, $query, $keywordLimit, $documentIds);

        // 4. Combine results using Reciprocal Rank Fusion (RRF)
        return $this->fuseResults($vectorResults, $keywordResults, $topK);
    }

    /**
     * Resolve document IDs to filter by.
     *
     * @return array<string>
     */
    protected function resolveDocumentIds(): array
    {
        if (app()->bound('ragbot.chatbot')) {
            return app('ragbot.chatbot')->documents()->pluck('rag_documents.id')->toArray();
        }

        return [];
    }

    /**
     * Fuse vector and keyword results using Reciprocal Rank Fusion.
     */
    protected function fuseResults(Collection $vectorResults, Collection $keywordResults, int $topK): Collection
    {
        $scores = [];
        $k = config('ragbot.retrieval.hybrid.rrf_k', self::RRF_K);

        // Rank the results
        $this->applyRrfScore($vectorResults, $scores, $k);
        $this->applyRrfScore($keywordResults, $scores, $k);

        // Sort by score descending
        arsort($scores);

        // Take top K IDs
        $topIds = array_slice(array_keys($scores), 0, $topK);

        if (empty($topIds)) {
            return collect();
        }

        // Return the actual chunk objects in the correct order
        $allChunks = $vectorResults->concat($keywordResults)->unique('id')->keyBy('id');

        return collect($topIds)->map(fn ($id) => $allChunks->get($id))->filter();
    }

    /**
     * Apply RRF score to chunks based on their rank in the collection.
     *
     * @param  array<string, float>  $scores
     */
    protected function applyRrfScore(Collection $results, array &$scores, int $k): void
    {
        $results->values()->each(function ($chunk, $index) use (&$scores, $k) {
            $rank = $index + 1;
            $score = 1 / ($k + $rank);

            $scores[$chunk->id] = ($scores[$chunk->id] ?? 0) + $score;
        });
    }
}
