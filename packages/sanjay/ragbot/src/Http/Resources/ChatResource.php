<?php

namespace Sanjay\Ragbot\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource for a single chat message.
 */
class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => $this->content,
            'session_id' => $this->conversation->session_id,
            'conversation_id' => $this->conversation_id,
            'timestamp' => $this->created_at->toISOString(),
        ];
    }
}
