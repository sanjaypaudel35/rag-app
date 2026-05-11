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
     * Create a new retrieval service instance.
     */
    public function __construct(
        protected EmbeddingManager $embeddingManager,
        protected VectorStoreManager $vectorStoreManager
    ) {}

    /**
     * Retrieves the most relevant chunks for a given query using vector similarity.
     */
    public function retrieve(Project $project, string $query, int $topK = 5): Collection
    {
        // 1. Generate embedding for the query
        $queryVector = $this->embeddingManager->embed($query, $project);

        // 2. Resolve document IDs. Prefer chatbot-scoped if a chatbot is bound.
        $documentIds = [];
        if (app()->bound('ragbot.chatbot')) {
            $chatbot = app('ragbot.chatbot');
            $documentIds = $chatbot->documents()->pluck('rag_documents.id')->toArray();
        }

        // 3. Search for similar chunks
        return $this->vectorStoreManager->search($project, $queryVector, $topK, $documentIds);
    }
}
