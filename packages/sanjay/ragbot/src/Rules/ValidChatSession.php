<?php

namespace Sanjay\Ragbot\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Sanjay\Ragbot\Contracts\Repositories\ConversationRepositoryInterface;

/**
 * Validation rule to ensure the chat session is valid and properly scoped.
 */
class ValidChatSession implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $project = app('ragbot.project');
        $chatbot = app()->bound('ragbot.chatbot') ? app('ragbot.chatbot') : null;

        $conversationRepository = app(ConversationRepositoryInterface::class);

        // 2. Validate against existing conversations to ensure scoping
        $conversation = $conversationRepository->findOneBy(['session_id' => $value]);

        if ($conversation) {
            // Must belong to the current project
            if ($conversation->project_id !== $project->id) {
                $fail('The session does not belong to the current project.');

                return;
            }

            // If a specific chatbot is authenticated (via Chatbot API Key),
            // the conversation must belong to that chatbot.
            if ($chatbot && $conversation->chatbot_id !== $chatbot->id) {
                $fail('The session does not belong to the authenticated chatbot.');

                return;
            }
        }

        // 3. Ensure the authenticated chatbot itself belongs to the project
        if ($chatbot && $chatbot->project_id !== $project->id) {
            $fail('The chatbot is not correctly linked to the project.');
        }
    }
}
