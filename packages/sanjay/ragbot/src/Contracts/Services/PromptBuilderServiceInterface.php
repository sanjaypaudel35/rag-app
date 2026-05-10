<?php

namespace Sanjay\Ragbot\Contracts\Services;

use Illuminate\Support\Collection;

/**
 * Interface for building LLM prompts.
 */
interface PromptBuilderServiceInterface
{
    /**
     * Build a prompt for the LLM using retrieved context and user query.
     */
    public function build(string $query, Collection $context): string;
}
