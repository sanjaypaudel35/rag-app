<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Illuminate\Support\Facades\Log;
use Sanjay\Ragbot\Contracts\Repositories\ConversationRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\MessageRepositoryInterface;
use Sanjay\Ragbot\Contracts\Services\PromptBuilderServiceInterface;
use Sanjay\Ragbot\Contracts\Services\RetrievalServiceInterface;
use Sanjay\Ragbot\Enums\MessageRole;
use Sanjay\Ragbot\Exceptions\LlmResponseException;
use Sanjay\Ragbot\Exceptions\RetrievalException;
use Sanjay\Ragbot\Models\Chatbot;
use Sanjay\Ragbot\Models\Conversation;

/**
 * Generic chat service to handle the RAG chat workflow.
 */
class ChatService
{
    /**
     * Create a new chat service instance.
     */
    public function __construct(
        protected ConversationRepositoryInterface $conversationRepository,
        protected MessageRepositoryInterface $messageRepository,
        protected RetrievalServiceInterface $retrievalService,
        protected PromptBuilderServiceInterface $promptBuilderService,
        protected LlmManager $llmManager
    ) {}

    /**
     * Process a user message and return the assistant response.
     *
     * @throws RetrievalException
     * @throws LlmResponseException
     */
    public function chat(Chatbot $chatbot, string $sessionId, string $userMessage): string
    {
        // 1. Get/create Conversation
        /** @var Conversation $conversation */
        $conversation = $this->conversationRepository->findOrCreate($chatbot->id, $sessionId);

        // 2. Store user message
        $this->messageRepository->create([
            'project_id' => $chatbot->project_id,
            'conversation_id' => $conversation->id,
            'role' => MessageRole::User,
            'content' => $userMessage,
        ]);

        // 3. Retrieve relevant chunks
        try {
            $chunks = $this->retrievalService->retrieve($chatbot, $userMessage);

            Log::info('Retrieved Chunks:', [
                'count' => $chunks->count(),
                'chunks' => $chunks->map(fn ($chunk) => [
                    'id' => $chunk->id,
                    'content' => $chunk->content,
                    'metadata' => $chunk->metadata,
                ])->toArray(),
            ]);
        } catch (\Exception $e) {
            throw new RetrievalException('Failed to retrieve relevant context: '.$e->getMessage(), 0, $e);
        }

        // 4. Build prompt
        $prompt = $this->promptBuilderService->build($userMessage, $chunks);

        // 5. Call LLM
        try {
            $assistantResponse = $this->llmManager->complete($prompt);
        } catch (LlmResponseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new LlmResponseException('LLM call failed: '.$e->getMessage(), 0, $e);
        }

        // 6. Store assistant message
        $this->messageRepository->create([
            'project_id' => $chatbot->project_id,
            'conversation_id' => $conversation->id,
            'role' => MessageRole::Assistant,
            'content' => $assistantResponse,
        ]);

        return $assistantResponse;
    }
}
