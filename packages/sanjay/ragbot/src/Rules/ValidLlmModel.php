<?php

namespace Sanjay\Ragbot\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Sanjay\Ragbot\Enums\LlmProvider;

class ValidLlmModel implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param  string|null  $providerValue  The value of the provider field.
     * @param  bool  $isEmbedding  Whether we are validating an embedding model.
     */
    public function __construct(
        protected ?string $providerValue,
        protected bool $isEmbedding = false
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $provider = LlmProvider::tryFrom($this->providerValue ?? '');

        if (! $provider) {
            $fail('The selected provider is invalid.');

            return;
        }

        $validModels = $this->isEmbedding
            ? $provider->embeddingModels()
            : $provider->models();

        if (! array_key_exists($value, $validModels)) {
            $type = $this->isEmbedding ? 'embedding' : 'chat';
            $fail("The selected {$type} model is not available for the {$provider->name} provider.");
        }
    }
}
