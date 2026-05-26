<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Collection;
use Sanjay\Ragbot\Contracts\Services\PromptBuilderServiceInterface;

/**
 * Service for building LLM prompts with context.
 */
class PromptBuilderService implements PromptBuilderServiceInterface
{
    /**
     * Constructs the LLM prompt from retrieved chunks and conversation history.
     */
    public function build(string $query, Collection $chunks, Collection $history): string
    {
        $context = $chunks->map(fn ($chunk) => $chunk->content)->implode("\n\n");

        $prompt = "You are a helpful AI assistant. Use the following pieces of context to answer the user's question.\n";
        $prompt .= "If you don't know the answer, just say that the relevent answer is not found in knowledge base don't try to make up an answer.\n\n";
        $prompt .= "CONTEXT:\n{$context}\n\n";

        if ($history->isNotEmpty()) {
            $prompt .= "CONVERSATION HISTORY:\n";
            foreach ($history as $message) {
                $role = ucfirst($message->role->value);
                $prompt .= "{$role}: {$message->content}\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "USER QUESTION: {$query}\n";
        $prompt .= 'ASSISTANT RESPONSE:';

        return $prompt;
    }
}
