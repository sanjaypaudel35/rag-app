<?php

namespace Sanjay\Ragbot\Support;

class VectorHelper
{
    /**
     * Calculates the cosine similarity between two vectors.
     *
     * @param  array<float>  $vec1
     * @param  array<float>  $vec2
     */
    public static function cosineSimilarity(array $vec1, array $vec2): float
    {
        $dotProduct = 0.0;
        $norm1 = 0.0;
        $norm2 = 0.0;

        $count = count($vec1);
        for ($i = 0; $i < $count; $i++) {
            $dotProduct += $vec1[$i] * $vec2[$i];
            $norm1 += $vec1[$i] ** 2;
            $norm2 += $vec2[$i] ** 2;
        }

        if ($norm1 == 0 || $norm2 == 0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($norm1) * sqrt($norm2));
    }
}
