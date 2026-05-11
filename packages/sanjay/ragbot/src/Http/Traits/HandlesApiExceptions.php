<?php

namespace Sanjay\Ragbot\Http\Traits;

use Illuminate\Http\JsonResponse;
use Sanjay\Ragbot\Exceptions\DocumentProcessingException;
use Sanjay\Ragbot\Exceptions\LlmResponseException;
use Sanjay\Ragbot\Exceptions\RetrievalException;
use Throwable;

/**
 * Trait to handle API-specific exceptions and return consistent JSON responses.
 */
trait HandlesApiExceptions
{
    /**
     * Handle the given exception.
     */
    protected function handleException(Throwable $e): JsonResponse
    {
        $status = match (true) {
            $e instanceof DocumentProcessingException => 422,
            $e instanceof RetrievalException => 502,
            $e instanceof LlmResponseException => 502,
            default => 500,
        };

        return response()->json([
            'error' => $e->getMessage(),
            'type' => class_basename($e),
        ], $status);
    }
}
