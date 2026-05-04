<?php

namespace Sanjay\Ragbot\Services\Tenant;

class ChunkingService
{
    /**
     * Splits document text content into overlapping chunks.
     *
     * @return array<string>
     */
    public function chunk(string $text, ?int $chunkSize = null, ?int $overlap = null): array
    {
        $chunkSize ??= config('ragbot.chunking.size', 500);
        $overlap ??= config('ragbot.chunking.overlap', 50);

        if (empty($text)) {
            return [];
        }

        $chunks = [];
        $textLength = mb_strlen($text);
        $start = 0;

        while ($start < $textLength) {
            $chunks[] = mb_substr($text, $start, $chunkSize);

            if ($start + $chunkSize >= $textLength) {
                break;
            }

            $start += ($chunkSize - $overlap);
        }

        return $chunks;
    }
}
