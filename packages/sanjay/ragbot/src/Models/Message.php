<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sanjay\Ragbot\Database\Factories\MessageFactory;
use Sanjay\Ragbot\Enums\MessageRole;
use Sanjay\Ragbot\Models\Traits\BelongsToProject;

/**
 * Model representing a chat message.
 *
 * @property string $id
 * @property string $project_id
 * @property string $conversation_id
 * @property MessageRole $role
 * @property string $content
 */
class Message extends Model
{
    use BelongsToProject;
    use HasFactory;
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rag_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'conversation_id',
        'role',
        'content',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'role' => MessageRole::class,
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return MessageFactory::new();
    }

    /**
     * Get the conversation that owns the message.
     *
     * @return BelongsTo<Conversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
