<?php

namespace Sanjay\Ragbot\Enums;

/**
 * Enum representing the role of a message sender.
 */
enum MessageRole: string
{
    case User = 'user';
    case Assistant = 'assistant';
}
