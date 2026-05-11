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
        <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 border-dashed">
            <div class="h-16 w-16 bg-indigo-50 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mb-4">
                <flux:icon icon="rocket-launch" class="text-indigo-600 dark:text-indigo-400 h-8 w-8" />
            </div>
            <flux:heading size="lg">Embeddable Widget: Coming Soon!</flux:heading>
            <flux:text class="mt-2 text-center max-w-md">We're working on a drop-in chat widget that you can add to any website with a single line of code. Stay tuned!</flux:text>
            
            <div class="mt-8 flex gap-4 opacity-50 grayscale pointer-events-none text-zinc-400">
                <div class="w-32 h-40 bg-zinc-200 dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-300 dark:border-zinc-700 p-2 flex flex-col">
                    <div class="w-full h-2 bg-zinc-300 dark:bg-zinc-700 rounded mb-2"></div>
                    <div class="w-3/4 h-2 bg-zinc-300 dark:bg-zinc-700 rounded mb-2"></div>
                    <div class="mt-auto flex justify-end">
                        <div class="w-6 h-6 rounded-full bg-indigo-400/50"></div>
                    </div>
                </div>
                <div class="w-32 h-40 bg-zinc-200 dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-300 dark:border-zinc-700 p-2">
                    <div class="w-full h-2 bg-zinc-300 dark:bg-zinc-700 rounded mb-2"></div>
                    <div class="w-full h-2 bg-zinc-300 dark:bg-zinc-700 rounded mb-2"></div>
                    <div class="w-1/2 h-2 bg-zinc-300 dark:bg-zinc-700 rounded mb-2"></div>
                </div>
            </div>
        </div>
    @endif
</div>
