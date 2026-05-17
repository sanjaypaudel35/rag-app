<?php

namespace Sanjay\Ragbot\Services\Tenant;

/**
 * Service to calculate the cost of LLM usage.
 */
class CostCalculator
{
    /**
     * Approximate costs per 1M tokens in USD.
     * These should ideally be in config or database.
     */
    protected array $costs = [
        'gpt-4o' => ['input' => 5.00, 'output' => 15.00],
        'gpt-4o-mini' => ['input' => 0.15, 'output' => 0.60],
        'claude-3-5-sonnet-20240620' => ['input' => 3.00, 'output' => 15.00],
        'claude-3-haiku-20240307' => ['input' => 0.25, 'output' => 1.25],
    ];

    /**
     * Calculate the cost for a given model and token usage.
     */
    public function calculate(string $model, int $inputTokens, int $outputTokens): float
    {
        $modelCosts = $this->costs[$model] ?? $this->costs['gpt-4o-mini'];

        $inputCost = ($inputTokens / 1_000_000) * $modelCosts['input'];
        $outputCost = ($outputTokens / 1_000_000) * $modelCosts['output'];

        return (float) ($inputCost + $outputCost);
    }
}
