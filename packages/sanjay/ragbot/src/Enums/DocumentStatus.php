<?php

namespace Sanjay\Ragbot\Enums;

/**
 * Enum representing the processing status of a document.
 */
enum DocumentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
}
