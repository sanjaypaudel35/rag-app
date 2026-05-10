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
     * Retrieve relevant chunks for a given query and chatbot.
     */
    public function retrieve(Chatbot $chatbot, string $query, int $topK = 5): Collection
    {
        /** @var Project $project */
        $project = $chatbot->project;

        // 1. Generate embedding for the query
        $queryVector = $this->embeddingManager->embed($query, $project);

        // 2. Get document IDs associated with the chatbot
        $documentIds = $chatbot->documents()->pluck('rag_documents.id')->toArray();

        // 3. Search for similar chunks within those documents
        return $this->vectorStoreManager->search($project, $queryVector, $topK, $documentIds);
    }
}
