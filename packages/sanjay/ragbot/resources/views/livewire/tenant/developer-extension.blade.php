<div>
    <div class="mb-8">
        <flux:heading size="xl">Developer Extension Guide</flux:heading>
        <flux:text class="mt-1">Extend and customize Ragbot's core capabilities to suit your specific architectural needs.</flux:text>
    </div>

    <div class="space-y-12 max-w-4xl">
        <!-- Introduction -->
        <section>
            <flux:heading size="lg" class="mb-4">Overview</flux:heading>
            <flux:text class="mb-6">Ragbot is built with extensibility at its core. You can easily swap out or extend the default embedding logic, vector stores, and LLM services while maintaining full compatibility with the existing RAG pipeline.</flux:text>
            
            <flux:callout variant="info" icon="information-circle" class="mb-8">
                All custom extensions should be registered in the <code class="px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-xs font-mono">boot</code> method of your <code class="px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-xs font-mono">AppServiceProvider</code> or a dedicated package service provider.
            </flux:callout>
        </section>

        <flux:separator />

        <!-- Custom LLM Service -->
        <section>
            <flux:heading size="lg" class="mb-2">1. Custom LLM Service</flux:heading>
            <flux:text class="mb-6">If you need to connect to a private LLM, use a specialized local model, or implement custom retry/logging logic for LLM calls, you can implement the <code class="font-mono text-xs">LlmInterface</code>.</flux:text>

            <div class="space-y-4">
                <flux:heading size="sm" class="uppercase tracking-wider text-zinc-500">Step A: Implement the Interface</flux:heading>
                <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>namespace App\Services;

use Sanjay\Ragbot\Contracts\Services\LlmInterface;

class MyCustomLlmService implements LlmInterface
{
    public function complete(string $prompt, array $options = []): string
    {
        // Your logic to call a local model or private API
        $response = $this->callMyModel($prompt, $options);
        
        return $response;
    }
}</code></pre>
                </div>

                <flux:heading size="sm" class="uppercase tracking-wider text-zinc-500">Step B: Register via LlmManager</flux:heading>
                <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>use Sanjay\Ragbot\Services\Tenant\LlmManager;

public function boot()
{
    $this->app->make(LlmManager::class)->extend('my-driver', function ($app) {
        return new \App\Services\MyCustomLlmService();
    });
}</code></pre>
                </div>
                
                <flux:text size="sm" class="mt-4">Once registered, your driver will be available for selection in the Project Settings.</flux:text>
            </div>
        </section>

        <flux:separator />

        <!-- Custom Embedding -->
        <section>
            <flux:heading size="lg" class="mb-2">2. Custom Embedding Logic</flux:heading>
            <flux:text class="mb-6">For local embedding generation or using custom providers, implement the <code class="font-mono text-xs">EmbeddingInterface</code>.</flux:text>

            <div class="space-y-4">
                <flux:heading size="sm" class="uppercase tracking-wider text-zinc-500">Step A: Create Implementation</flux:heading>
                <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>namespace App\Services;

use Sanjay\Ragbot\Contracts\Services\EmbeddingInterface;
use Sanjay\Ragbot\Models\Project;

class LocalEmbeddingService implements EmbeddingInterface
{
    public function embed(string $text, ?Project $project = null): array
    {
        // Custom logic to generate vector (e.g., using a local Python bridge)
        return $this->callLocalModel($text);
    }
}</code></pre>
                </div>

                <flux:heading size="sm" class="uppercase tracking-wider text-zinc-500">Step B: Register Extension</flux:heading>
                <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>use Sanjay\Ragbot\Services\Tenant\EmbeddingManager;

public function boot()
{
    $this->app->make(EmbeddingManager::class)->extend('local-fast', function ($app) {
        return new \App\Services\LocalEmbeddingService();
    });
}</code></pre>
                </div>
            </div>
        </section>

        <flux:separator />

        <!-- Custom Vector Store -->
        <section>
            <flux:heading size="lg" class="mb-2">3. Custom Vector Store</flux:heading>
            <flux:text class="mb-6">Implement the <code class="font-mono text-xs">VectorStoreInterface</code> to use hosted solutions like Pinecone or Weaviate.</flux:text>

            <div class="space-y-4">
                <flux:heading size="sm" class="uppercase tracking-wider text-zinc-500">Step A: Implement Interface</flux:heading>
                <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>namespace App\Services;

use Sanjay\Ragbot\Contracts\Services\VectorStoreInterface;
use Illuminate\Support\Collection;
use Sanjay\Ragbot\Models\Project;

class HostedVectorStore implements VectorStoreInterface
{
    public function store(Project $project, string $chunkId, array $vector): void 
    {
        // Push vector to hosted API
    }

    public function search(Project $project, array $queryVector, int $topK = 5, array $documentIds = []): Collection
    {
        // Query hosted API and return Chunk collection
    }
}</code></pre>
                </div>

                <flux:heading size="sm" class="uppercase tracking-wider text-zinc-500">Step B: Register Extension</flux:heading>
                <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>use Sanjay\Ragbot\Services\Tenant\VectorStoreManager;

public function boot()
{
    $this->app->make(VectorStoreManager::class)->extend('my-cloud-store', function ($app) {
        return new \App\Services\HostedVectorStore();
    });
}</code></pre>
                </div>
            </div>
        </section>
    </div>
</div>
