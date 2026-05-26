<?php

namespace Sanjay\Ragbot\Services\Tenant;

use Sanjay\Ragbot\Contracts\Repositories\ChatbotRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\ConversationRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\MessageRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\ProjectSettingRepositoryInterface;
use Sanjay\Ragbot\Contracts\Services\PromptBuilderServiceInterface;
use Sanjay\Ragbot\Contracts\Services\RetrievalServiceInterface;
use Sanjay\Ragbot\Enums\MessageRole;
use Sanjay\Ragbot\Exceptions\ChatbotInactiveException;
use Sanjay\Ragbot\Exceptions\LlmResponseException;
use Sanjay\Ragbot\Exceptions\RetrievalException;
use Sanjay\Ragbot\Models\Chatbot;
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
        protected ChatbotRepositoryInterface $chatbotRepository,
        protected ProjectSettingRepositoryInterface $projectSettingRepository,
        protected RetrievalServiceInterface $retrievalService,
        protected PromptBuilderServiceInterface $promptBuilderService,
        protected LlmManager $llmManager,
        protected CostCalculator $costCalculator
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
        /** @var Chatbot|null $chatbot */
        $chatbot = app()->bound('ragbot.chatbot') ? app('ragbot.chatbot') : null;
        $chatbotId = $chatbot?->id;

        if ($chatbot && ! $chatbot->is_active) {
            throw new ChatbotInactiveException('This chatbot is currently inactive and cannot respond to messages.');
        }

        /** @var Conversation $conversation */
        $conversation = $this->conversationRepository->findOrCreate($chatbotId, $sessionId);

        // Increment conversation count if new
        if ($conversation->wasRecentlyCreated) {
            if ($chatbotId) {
                $this->chatbotRepository->increment($chatbotId, 'total_conversations');
            }

            $this->projectSettingRepository->incrementForProject($project->id, 'total_conversations');
        }

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
            $llmResponse = $this->llmManager->complete($prompt);
        } catch (\Exception $e) {
            throw new LlmResponseException('LLM call failed: '.$e->getMessage(), 0, $e);
        }

        // 6. Calculate cost
        $cost = $this->costCalculator->calculate(
            $llmResponse->model ?? 'gpt-4o-mini',
            $llmResponse->inputTokens,
            $llmResponse->outputTokens
        );

        // 7. Store assistant message with tokens and model
        $this->messageRepository->create([
            'project_id' => $project->id,
            'conversation_id' => $conversation->id,
            'role' => MessageRole::Assistant,
            'content' => $llmResponse->content,
            'model' => $llmResponse->model,
            'input_tokens' => $llmResponse->inputTokens,
            'output_tokens' => $llmResponse->outputTokens,
        ]);

        // 8. Update aggregates
        if ($chatbotId) {
            $this->chatbotRepository->increment($chatbotId, 'total_tokens_used', $llmResponse->totalTokens());
            $this->chatbotRepository->increment($chatbotId, 'total_input_tokens', $llmResponse->inputTokens);
            $this->chatbotRepository->increment($chatbotId, 'total_output_tokens', $llmResponse->outputTokens);
            $this->chatbotRepository->increment($chatbotId, 'total_cost', $cost);

            // Update per-model usage
            $this->chatbotRepository->updateModelUsage(
                $chatbotId,
                $llmResponse->model ?? 'unknown',
                $llmResponse->inputTokens,
                $llmResponse->outputTokens,
                $cost
            );
        }

        $this->projectSettingRepository->incrementForProject($project->id, 'total_tokens_used', $llmResponse->totalTokens());
        $this->projectSettingRepository->incrementForProject($project->id, 'total_input_tokens', $llmResponse->inputTokens);
        $this->projectSettingRepository->incrementForProject($project->id, 'total_output_tokens', $llmResponse->outputTokens);
        $this->projectSettingRepository->incrementForProject($project->id, 'total_cost', $cost);

        return $llmResponse->content;
    }
}
