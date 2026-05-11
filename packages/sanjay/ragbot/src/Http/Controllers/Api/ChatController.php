<?php

namespace Sanjay\Ragbot\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Sanjay\Ragbot\Contracts\Repositories\ConversationRepositoryInterface;
use Sanjay\Ragbot\Enums\MessageRole;
use Sanjay\Ragbot\Http\Requests\ChatFormRequest;
use Sanjay\Ragbot\Http\Resources\ChatResource;
use Sanjay\Ragbot\Http\Traits\HandlesApiExceptions;
use Sanjay\Ragbot\Services\Tenant\ChatService;
use Throwable;

/**
 * Controller for the Chat API.
 */
class ChatController extends Controller
{
    use HandlesApiExceptions;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ChatService $chatService,
        protected ConversationRepositoryInterface $conversationRepository
    ) {}

    /**
     * Handle the chat request.
     */
    public function __invoke(ChatFormRequest $request)
    {
        try {
            $project = app('ragbot.project');
            $sessionId = $request->input('session_id') ?? (string) Str::uuid();
            $message = $request->input('message');

            $this->chatService->chat($project, $sessionId, $message);

            // Fetch the conversation using repository with closure
            $conversation = $this->conversationRepository->findOne(function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId);

                $chatbot = app()->bound('ragbot.chatbot') ? app('ragbot.chatbot') : null;
                if ($chatbot) {
                    $query->where('chatbot_id', $chatbot->id);
                }
            });

            if (! $conversation) {
                throw new \Exception('Conversation not found after chat processing.');
            }

            $lastMessage = $conversation->messages()
                ->where('role', MessageRole::Assistant)
                ->latest()
                ->first();

            return new ChatResource($lastMessage);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }
}
