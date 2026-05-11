<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sanjay\Ragbot\Database\Factories\ChatbotFactory;

/**
 * Model representing a Chatbot.
 *
 * @property string $id
 * @property string $project_id
 * @property string $name
 * @property string $api_key
 * @property int $total_tokens_used
 * @property int $total_conversations
 */
class Chatbot extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ChatbotFactory::new();
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rag_chatbots';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'name',
        'api_key',
        'allowed_origins',
        'total_tokens_used',
        'total_conversations',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'allowed_origins' => 'array',
        'total_tokens_used' => 'integer',
        'total_conversations' => 'integer',
    ];

    /**
     * Get the project that owns the chatbot.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the documents associated with the chatbot.
     *
     * @return BelongsToMany<Document, $this>
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'rag_chatbot_documents', 'chatbot_id', 'document_id')
            ->using(ChatbotDocument::class);
    }

    /**
     * Get the conversations associated with the chatbot.
     *
     * @return HasMany<Conversation, $this>
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
