<?php

namespace Sanjay\Ragbot\Enums;

/**
 * Enum representing supported vector storage drivers.
 */
enum VectorStore: string
{
    case MySql = 'mysql';
    case PgVector = 'pgvector';
    case Custom = 'custom';
}
