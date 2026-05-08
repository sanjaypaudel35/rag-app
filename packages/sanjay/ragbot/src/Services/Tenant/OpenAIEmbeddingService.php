<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Facades\Http;
use Sanjay\Ragbot\Contracts\Services\EmbeddingInterface;
use Sanjay\Ragbot\Exceptions\EmbeddingException;
use Sanjay\Ragbot\Models\Project;

/**
 * Service for interacting with OpenAI's Embeddings API.
 */
class OpenAIEmbeddingService implements EmbeddingInterface
{
    /**
     * The project instance.
     */
    protected Project $project;

    /**
     * The project settings.
     */
    protected $settings;

    /**
     * Create a new service instance.
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->settings = $project->settings()->withoutGlobalScope('project')->first();
    }

    /**
     * Generate a vector embedding for the given text.
     *
     * @return array<float>
     *
     * @throws EmbeddingException
     */
    public function embed(string $text, ?Project $project = null): array
    {
        $apiKey = $this->settings->llm_api_key;
        $baseUrl = $this->settings->embedding_api_endpoint ?? 'https://api.openai.com/v1';
        $model = $this->settings->llm_model_for_embedding ?? config('ragbot.embedding.providers.openai.model');

        $response = Http::withToken($apiKey)
            ->baseUrl($baseUrl)
            ->post('/embeddings', [
                'model' => $model,
                'input' => $text,
            ]);

        if ($response->failed()) {
            throw new EmbeddingException('OpenAI Embedding API error: '.$response->body());
        }

        return $response->json('data.0.embedding') ?? [];
    }
}
