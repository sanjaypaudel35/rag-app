<?php

namespace Sanjay\Ragbot\Contracts\Services;

use Illuminate\Support\Collection;
use Sanjay\Ragbot\Models\Chatbot;

/**
 * Interface for document retrieval service.
 */
interface RetrievalServiceInterface
{
    /**
     * Retrieve relevant chunks for a given query and chatbot.
     */
    public function retrieve(Chatbot $chatbot, string $query, int $topK = 5): Collection;
}
