<div>
    <div class="mb-8">
        <flux:heading size="xl">Integration Guide</flux:heading>
        <flux:text class="mt-1">Connect your application with our AI-powered RAG engine.</flux:text>
    </div>

    <!-- Custom Tab Implementation for Flux Free -->
    <div class="flex gap-2 mb-8 border-b border-zinc-200 dark:border-zinc-800 pb-px">
        <button 
            wire:click="$set('activeTab', 'api')" 
            class="px-4 py-2 text-sm font-medium transition-colors border-b-2 {{ $activeTab === 'api' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            <div class="flex items-center gap-2">
                <flux:icon icon="code-bracket" variant="mini" />
                API Integration
            </div>
        </button>
        <button 
            wire:click="$set('activeTab', 'widget')" 
            class="px-4 py-2 text-sm font-medium transition-colors border-b-2 {{ $activeTab === 'widget' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            <div class="flex items-center gap-2">
                <flux:icon icon="window" variant="mini" />
                Widget Integration
            </div>
        </button>
        <button 
            wire:click="$set('activeTab', 'developer')" 
            class="px-4 py-2 text-sm font-medium transition-colors border-b-2 {{ $activeTab === 'developer' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            <div class="flex items-center gap-2">
                <flux:icon icon="wrench-screwdriver" variant="mini" />
                Developer Extension
            </div>
        </button>
    </div>

    @if($activeTab === 'api')
        <div class="space-y-8 max-w-4xl">
            <!-- API Introduction -->
            <section>
                <flux:heading size="lg" class="mb-2">Authentication</flux:heading>
                <flux:text>All API requests must include the <code class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs font-mono text-zinc-900 dark:text-zinc-100">X-Api-Key</code> header. You can use your Chatbot-specific API key (recommended for frontend/client use) or your Project Master Key (for administrative tasks).</flux:text>
                
                <div class="mt-4 p-4 bg-zinc-100 dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 font-mono text-sm">
                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">X-Api-Key:</span> <span class="text-zinc-600 dark:text-zinc-400 text-xs">rb_c_your_chatbot_key_here</span>
                </div>
            </section>

            <flux:separator />

            <!-- Chat Endpoint -->
            <section>
                <div class="flex items-center gap-3 mb-4">
                    <flux:badge variant="primary" size="sm">POST</flux:badge>
                    <code class="px-2 py-1 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-sm font-mono text-zinc-900 dark:text-zinc-100">{{ url(config('ragbot.prefix', 'ragbot') . '/api/v1/chat') }}</code>
                </div>
                
                <flux:heading size="lg" class="mb-4">Send a Message</flux:heading>
                <flux:text class="mb-6">Send a user message to the RAG pipeline and receive a context-aware AI response.</flux:text>

                <div class="space-y-6">
                    <div>
                        <flux:heading size="sm" class="mb-2 uppercase tracking-wider text-zinc-500">Request Body</flux:heading>
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>Field</flux:table.column>
                                <flux:table.column>Type</flux:table.column>
                                <flux:table.column>Required</flux:table.column>
                                <flux:table.column>Description</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                <flux:table.row>
                                    <flux:table.cell><code class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs font-mono">message</code></flux:table.cell>
                                    <flux:table.cell>string</flux:table.cell>
                                    <flux:table.cell>Yes</flux:table.cell>
                                    <flux:table.cell>The user's question or message (max 500 characters).</flux:table.cell>
                                </flux:table.row>
                                <flux:table.row>
                                    <flux:table.cell><code class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs font-mono">session_id</code></flux:table.cell>
                                    <flux:table.cell>string</flux:table.cell>
                                    <flux:table.cell>No</flux:table.cell>
                                    <flux:table.cell>A unique identifier for the user's session. If not provided, a unique ID will be generated and returned in the response.</flux:table.cell>
                                </flux:table.row>                            </flux:table.rows>
                        </flux:table>
                    </div>

                    <div>
                        <flux:heading size="sm" class="mb-2 uppercase tracking-wider text-zinc-500">Success Response (200 OK)</flux:heading>
                        <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>{
    "data": {
        "message": "Based on the provided documents, you can install the package using composer...",
        "session_id": "user-session-123",
        "conversation_id": "uuid-of-conversation",
        "timestamp": "2026-05-12T10:00:00Z"
    }
}</code></pre>
                        </div>
                    </div>

                    <div>
                        <flux:heading size="sm" class="mb-2 uppercase tracking-wider text-zinc-500">Error Responses</flux:heading>
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>Code</flux:table.column>
                                <flux:table.column>Type</flux:table.column>
                                <flux:table.column>Description</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                <flux:table.row>
                                    <flux:table.cell>401</flux:table.cell>
                                    <flux:table.cell>Unauthorized</flux:table.cell>
                                    <flux:table.cell>Missing or invalid API key.</flux:table.cell>
                                </flux:table.row>
                                <flux:table.row>
                                    <flux:table.cell>422</flux:table.cell>
                                    <flux:table.cell>ValidationException</flux:table.cell>
                                    <flux:table.cell>Input validation failed (e.g. message too long).</flux:table.cell>
                                </flux:table.row>
                                <flux:table.row>
                                    <flux:table.cell>429</flux:table.cell>
                                    <flux:table.cell>RateLimitException</flux:table.cell>
                                    <flux:table.cell>Too many requests. Check the <code class="px-1 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[10px] font-mono">type</code> field for <code class="px-1 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[10px] font-mono">ChatbotRateLimitException</code> or <code class="px-1 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[10px] font-mono">SessionRateLimitException</code>.</flux:table.cell>
                                </flux:table.row>
                                <flux:table.row>
                                    <flux:table.cell>502</flux:table.cell>
                                    <flux:table.cell>LlmResponseException</flux:table.cell>
                                    <flux:table.cell>The LLM provider (OpenAI/Anthropic) returned an error.</flux:table.cell>
                                </flux:table.row>
                            </flux:table.rows>
                        </flux:table>
                    </div>
                </div>
            </section>

            <flux:separator />

            <!-- Conversation Management -->
            <section>
                <flux:heading size="lg" class="mb-4">Conversation & Session Management</flux:heading>
                <flux:text class="mb-4">To maintain a coherent conversation history and provide context to the AI, you must reuse the <code class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs font-mono">session_id</code> across multiple requests.</flux:text>
                
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="flex-none w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">1</div>
                        <div>
                            <flux:heading size="sm" class="mb-1">First Request</flux:heading>
                            <flux:text>Send your first message without a <code class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs font-mono">session_id</code> (or provide your own custom identifier).</flux:text>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-none w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">2</div>
                        <div>
                            <flux:heading size="sm" class="mb-1">Capture the ID</flux:heading>
                            <flux:text>The API will return a <code class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs font-mono">session_id</code> in the response payload. Capture this value in your frontend or integration layer.</flux:text>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-none w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">3</div>
                        <div>
                            <flux:heading size="sm" class="mb-1">Maintain History</flux:heading>
                            <flux:text>Include the captured <code class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs font-mono">session_id</code> in all subsequent requests for that user. The RAG engine will automatically retrieve the previous messages to maintain context.</flux:text>
                        </div>
                    </div>
                </div>
            </section>

            <flux:separator />

            <!-- Rate Limiting -->
            <section>
                <flux:heading size="lg" class="mb-4">Rate Limiting</flux:heading>
                <flux:text class="mb-4">The API enforces two layers of rate limiting to ensure performance and prevent abuse:</flux:text>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <flux:card variant="subtle">
                        <flux:heading size="sm" class="mb-1">Global Limit</flux:heading>
                        <flux:text size="sm">The total number of requests this chatbot can process across all users per minute.</flux:text>
                    </flux:card>
                    <flux:card variant="subtle">
                        <flux:heading size="sm" class="mb-1">Session Limit</flux:heading>
                        <flux:text size="sm">The maximum number of requests allowed for a single <code class="px-1 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[10px] font-mono">session_id</code> per minute.</flux:text>
                    </flux:card>
                </div>

                <flux:heading size="sm" class="mb-2">Handling Rate Limit Errors</flux:heading>
                <flux:text class="mb-4">When a limit is reached, the API returns a <code class="px-1 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[10px] font-mono">429 Too Many Requests</code> status. We recommend handling this gracefully in your UI:</flux:text>

                <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>const response = await fetch('/ragbot/api/v1/chat', { ... });

if (response.status === 429) {
    const errorData = await response.json();
    // Example: Show a notification to the user
    showToast("error", "You're sending messages too fast. Please wait a moment.");
    return;
}</code></pre>
                </div>
            </section>

            <flux:separator />

            <!-- Implementation Example -->
            <section>
                <flux:heading size="lg" class="mb-4">cURL Example</flux:heading>
                <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>curl -X POST {{ url(config('ragbot.prefix', 'ragbot') . '/api/v1/chat') }} \
     -H "X-Api-Key: your_chatbot_key" \
     -H "Content-Type: application/json" \
     -d '{
           "message": "Hello, how do I get started?",
           "session_id": "session_unique_id"
         }'</code></pre>
                </div>
            </section>
        </div>
    @elseif($activeTab === 'widget')
        <div class="space-y-8 max-w-4xl">
            <section>
                <flux:heading size="lg" class="mb-2">Chat Widget Integration</flux:heading>
                <flux:text>Add the following script tag to your website's <code class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs font-mono">&lt;body&gt;</code> to embed the chat widget. You must use your Project Master Key or a Chatbot-specific API key.</flux:text>
                
                <div class="mt-6 space-y-4">
                    <flux:heading size="sm" class="uppercase tracking-wider text-zinc-500">Embed Snippet</flux:heading>
                    <div class="relative group">
                        <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>&lt;script 
    src="{{ url(config('ragbot.prefix', 'ragbot') . '/api/widget.js?api_key=') }}YOUR_API_KEY"
    data-api-key="YOUR_API_KEY"
    data-base-url="{{ url('/') }}"
&gt;&lt;/script&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                    <div class="flex gap-3">
                        <flux:icon icon="exclamation-triangle" class="text-amber-600 dark:text-amber-400 shrink-0" />
                        <div>
                            <flux:heading size="sm" class="text-amber-800 dark:text-amber-300">Important: CORS Configuration</flux:heading>
                            <flux:text size="sm" class="mt-1 text-amber-700 dark:text-amber-400">
                                Ensure your website's origin (e.g., <code class="text-xs font-mono">https://example.com</code>) is added to the <strong>Allowed Origins</strong> in your Chatbot settings. If you use the Project Master Key, make sure the widget is enabled in your Project Settings.
                            </flux:text>
                        </div>
                    </div>
                </div>
            </section>

            <flux:separator />

            <section>
                <flux:heading size="lg" class="mb-4">Widget Configuration</flux:heading>
                <flux:text class="mb-6">You can customize the look and feel of your widget directly from the <a href="{{ route('ragbot.settings', ['project_slug' => $project->slug]) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Project Settings</a> page.</flux:text>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <flux:card variant="subtle" class="flex flex-col items-center text-center p-6">
                        <flux:icon icon="swatch" class="mb-4 text-indigo-600 dark:text-indigo-400" />
                        <flux:heading size="sm">Brand Color</flux:heading>
                        <flux:text size="sm">Match the widget to your brand identity.</flux:text>
                    </flux:card>

                    <flux:card variant="subtle" class="flex flex-col items-center text-center p-6">
                        <flux:icon icon="bars-3-bottom-left" class="mb-4 text-indigo-600 dark:text-indigo-400" />
                        <flux:heading size="sm">Custom Title</flux:heading>
                        <flux:text size="sm">Set a friendly greeting for your users.</flux:text>
                    </flux:card>

                    <flux:card variant="subtle" class="flex flex-col items-center text-center p-6">
                        <flux:icon icon="arrows-right-left" class="mb-4 text-indigo-600 dark:text-indigo-400" />
                        <flux:heading size="sm">Positioning</flux:heading>
                        <flux:text size="sm">Choose between left or right placement.</flux:text>
                    </flux:card>
                </div>
            </section>
        </div>
    @endif
</div>
ustom extensions, you must register them in the <code class="px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-xs font-mono">boot</code> method of your <code class="px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-xs font-mono">AppServiceProvider</code>.
                </flux:callout>
            </section>

            <flux:separator />

            <!-- Custom Embedding -->
            <section>
                <flux:heading size="lg" class="mb-2">1. Custom Embedding Logic</flux:heading>
                <flux:text class="mb-6">If you need to use a local embedding model, a specific private API, or custom pre-processing logic, you can implement the <code class="font-mono text-xs">EmbeddingInterface</code> and register it.</flux:text>

                <div class="space-y-4">
                    <flux:heading size="sm" class="uppercase tracking-wider text-zinc-500">Step A: Create your Implementation</flux:heading>
                    <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>namespace App\Services;

use Sanjay\Ragbot\Contracts\Services\EmbeddingInterface;
use Sanjay\Ragbot\Models\Project;

class LocalEmbeddingService implements EmbeddingInterface
{
    public function embed(string $text, ?Project $project = null): array
    {
        // Your custom logic to generate 1536-dimensional vector
        return $this->callLocalModel($text);
    }
}</code></pre>
                    </div>

                    <flux:heading size="sm" class="uppercase tracking-wider text-zinc-500">Step B: Register via Manager</flux:heading>
                    <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>use Sanjay\Ragbot\Services\Tenant\EmbeddingManager;

public function boot()
{
    $this->app->make(EmbeddingManager::class)->extend('my-local-driver', function ($app, $project) {
        return new \App\Services\LocalEmbeddingService();
    });
}</code></pre>
                    </div>
                    
                    <flux:text size="sm" class="mt-4">Once registered, you can select <code class="font-mono text-xs">my-local-driver</code> as your embedding driver in the Project Settings.</flux:text>
                </div>
            </section>

            <flux:separator />

            <!-- Custom Vector Store -->
            <section>
                <flux:heading size="lg" class="mb-2">2. Custom Vector Store</flux:heading>
                <flux:text class="mb-6">Need to use Pinecone, Weaviate, or a custom internal database? Implement the <code class="font-mono text-xs">VectorStoreInterface</code> and extend the manager.</flux:text>

                <div class="space-y-4">
                    <flux:heading size="sm" class="uppercase tracking-wider text-zinc-500">Step A: Implement Interface</flux:heading>
                    <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>namespace App\Services;

use Sanjay\Ragbot\Contracts\Services\VectorStoreInterface;
use Illuminate\Support\Collection;
use Sanjay\Ragbot\Models\Project;

class PineconeStore implements VectorStoreInterface
{
    public function store(Project $project, string $chunkId, array $vector): void 
    {
        // Logic to store in Pinecone
    }

    public function search(Project $project, array $queryVector, int $topK = 5, array $documentIds = []): Collection
    {
        // Logic to search Pinecone and return Collection of chunk IDs
    }
}</code></pre>
                    </div>

                    <flux:heading size="sm" class="uppercase tracking-wider text-zinc-500">Step B: Register Extension</flux:heading>
                    <div class="bg-zinc-900 text-zinc-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre class="text-xs leading-relaxed"><code>use Sanjay\Ragbot\Services\Tenant\VectorStoreManager;

public function boot()
{
    $this->app->make(VectorStoreManager::class)->extend('pinecone', function ($app) {
        return new \App\Services\PineconeStore();
    });
}</code></pre>
                    </div>

                    <flux:text size="sm" class="mt-4">In the LLM Settings tab, choose <strong>Custom</strong> for Vector Store and enter <code class="font-mono text-xs">pinecone</code> as the custom name.</flux:text>
                </div>
            </section>
        </div>
    @endif
</div>
