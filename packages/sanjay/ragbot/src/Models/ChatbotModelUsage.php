<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sanjay\Ragbot\Models\Traits\BelongsToProject;

/**
 * Model representing aggregated token usage per model for a chatbot.
 */
class ChatbotModelUsage extends Model
{
    use BelongsToProject;
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rag_chatbot_model_usage';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'chatbot_id',
        'model',
        'input_tokens',
        'output_tokens',
        'cost',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'input_tokens' => 'integer',
        'output_tokens' => 'integer',
        'cost' => 'decimal:6',
    ];

    /**
     * Get the chatbot that owns the usage record.
     *
     * @return BelongsTo<Chatbot, $this>
     */
    public function chatbot(): BelongsTo
    {
        return $this->belongsTo(Chatbot::class);
    }
}
