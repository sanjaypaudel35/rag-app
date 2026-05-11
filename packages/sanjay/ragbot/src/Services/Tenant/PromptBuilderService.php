<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Collection;
use Sanjay\Ragbot\Contracts\Services\PromptBuilderServiceInterface;
use Sanjay\Ragbot\Models\Chunk;

/**
 * Service for building LLM prompts with context.
 */
class PromptBuilderService implements PromptBuilderServiceInterface
{
    /**
     * Build a prompt for the LLM using retrieved context and user query.
     *
     * @param  Collection<int, Chunk>  $context
     */
    public function build(string $query, Collection $context): string
    {
        $contextString = $context->map(fn (Chunk $chunk) => $chunk->content)->implode("\n\n---\n\n");

        return <<<PROMPT
You are a helpful assistant. Use the following pieces of retrieved context to answer the user's question. Use the simple language to give the answer and alwasy
give the answer in friendly way, act like real customer support agent.


Context:
{$contextString}

Question: {$query}

Answer:
PROMPT;
    }
}
