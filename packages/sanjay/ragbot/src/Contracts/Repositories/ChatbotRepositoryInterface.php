<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface for Chatbot repository.
 */
interface ChatbotRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a chatbot by its unique API key.
     */
    public function findByApiKey(string $apiKey): ?Model;
}
