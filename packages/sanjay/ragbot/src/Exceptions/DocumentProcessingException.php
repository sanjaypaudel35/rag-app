<?php

namespace Sanjay\Ragbot\Exceptions;

use Exception;

/**
 * Exception thrown when document processing fails.
 */
class DocumentProcessingException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
