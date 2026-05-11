<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Sanjay\Ragbot\Contracts\Repositories\ConversationRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\MessageRepositoryInterface;
use Sanjay\Ragbot\Contracts\Services\PromptBuilderServiceInterface;
use Sanjay\Ragbot\Contracts\Services\RetrievalServiceInterface;
use Sanjay\Ragbot\Enums\MessageRole;
use Sanjay\Ragbot\Exceptions\LlmResponseException;
use Sanjay\Ragbot\Exceptions\RetrievalException;
use Sanjay\Ragbot\Models\Conversation;
use Sanjay\Ragbot\Models\Project;

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
     * Orchestrates the full RAG pipeline from query to response.
     *
     * @throws RetrievalException
     * @throws LlmResponseException
     */
    public function chat(Project $project, string $sessionId, string $userMessage): string
    {
        // 1. Get/create Conversation
        $chatbot = app()->bound('ragbot.chatbot') ? app('ragbot.chatbot') : null;
        $chatbotId = $chatbot?->id;

        /** @var Conversation $conversation */
        $conversation = $this->conversationRepository->findOrCreate($chatbotId, $sessionId);

        // 2. Store user message
        $this->messageRepository->create([
            'project_id' => $project->id,
            'conversation_id' => $conversation->id,
            'role' => MessageRole::User,
            'content' => $userMessage,
        ]);

        // 3. Retrieve relevant chunks
        try {
            $chunks = $this->retrievalService->retrieve($project, $userMessage);
        } catch (\Exception $e) {
            throw new RetrievalException('Failed to retrieve relevant context: '.$e->getMessage(), 0, $e);
        }

        // 4. Build prompt
        $history = $conversation->messages()->orderBy('created_at', 'asc')->get();
        $prompt = $this->promptBuilderService->build($userMessage, $chunks, $history);

        // 5. Call LLM
        try {
            $assistantResponse = $this->llmManager->complete($prompt);
        } catch (\Exception $e) {
            throw new LlmResponseException('LLM call failed: '.$e->getMessage(), 0, $e);
        }

        // 6. Store assistant message
        $this->messageRepository->create([
            'project_id' => $project->id,
            'conversation_id' => $conversation->id,
            'role' => MessageRole::Assistant,
            'content' => $assistantResponse,
        ]);

        return $assistantResponse;
    }
}
