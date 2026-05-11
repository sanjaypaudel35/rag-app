<?php

namespace Sanjay\Ragbot\Contracts\Services;

use Illuminate\Support\Collection;

/**
 * Interface for building LLM prompts.
 */
interface PromptBuilderServiceInterface
{
    /**
     * Constructs the LLM prompt from retrieved chunks and conversation history.
     */
    public function build(string $query, Collection $chunks, Collection $history): string;
}
