<?php

namespace Sanjay\Ragbot\Contracts\Services;

use Sanjay\Ragbot\DTOs\LlmResponse;
use Sanjay\Ragbot\Exceptions\LlmResponseException;

/**
 * Interface for Large Language Model providers.
 */
interface LlmInterface
{
    /**
     * Generate a completion for the given prompt.
     *
     * @param  array<string, mixed>  $options
     *
     * @throws LlmResponseException
     */
    public function complete(string $prompt, array $options = []): LlmResponse;
}
