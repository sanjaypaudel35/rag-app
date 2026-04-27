<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sanjay\Ragbot\Database\Factories\ConversationFactory;
use Sanjay\Ragbot\Models\Traits\BelongsToProject;

/**
 * Model representing a chat conversation.
 *
 * @property string $id
 * @property string $project_id
 * @property string $session_id
 * @property array|null $metadata
 */
class Conversation extends Model
{
    use HasUuids;
    use HasFactory;
    use BelongsToProject;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "rag_conversations";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "project_id",
        "session_id",
        "metadata",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "metadata" => "array",
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ConversationFactory::new();
    }

    /**
     * Get the messages associated with the conversation.
     *
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
