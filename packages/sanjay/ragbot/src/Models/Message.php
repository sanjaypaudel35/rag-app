<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sanjay\Ragbot\Enums\MessageRole;

/**
 * Model representing a chat message.
 *
 * @property string 
 * @property string 
 * @property string 
 * @property MessageRole 
 * @property string 
 */
class Message extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected  = "rag_messages";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected  = [
        "project_id",
        "conversation_id",
        "role",
        "content",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected  = [
        "role" => MessageRole::class,
    ];

    /**
     * Get the project that owns the message.
     *
     * @return BelongsTo<Project, >
     */
    public function project(): BelongsTo
    {
        return ->belongsTo(Project::class);
    }

    /**
     * Get the conversation that owns the message.
     *
     * @return BelongsTo<Conversation, >
     */
    public function conversation(): BelongsTo
    {
        return ->belongsTo(Conversation::class);
    }
}
