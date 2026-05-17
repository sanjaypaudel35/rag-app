<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Sanjay\Ragbot\Database\Factories\ProjectSettingFactory;
use Sanjay\Ragbot\Enums\LlmProvider;
use Sanjay\Ragbot\Enums\VectorStore;
use Sanjay\Ragbot\Models\Traits\BelongsToProject;

/**
 * Model representing Project-specific settings.
 *
 * @property string $id
 * @property string $project_id
 * @property LlmProvider $llm_provider
 * @property string|null $llm_api_key
 * @property string|null $llm_model
 * @property string|null $llm_model_for_embedding
 * @property string|null $llm_api_endpoint
 * @property string|null $embedding_api_endpoint
 * @property VectorStore $vector_store
 * @property bool $widget_enabled
 * @property int $total_tokens_used
 */
class ProjectSetting extends Model
{
    use BelongsToProject;
    use HasFactory;
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rag_project_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'llm_provider',
        'llm_api_key',
        'llm_model',
        'llm_model_for_embedding',
        'llm_api_endpoint',
        'embedding_api_endpoint',
        'vector_store',
        'vector_store_custom_name',
        'widget_enabled',
        'widget_color',
        'widget_title',
        'widget_logo',
        'widget_position',
        'widget_full_page',
        'total_tokens_used',
        'total_input_tokens',
        'total_output_tokens',
        'total_cost',
        'total_conversations',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'llm_provider' => LlmProvider::class,
        'vector_store' => VectorStore::class,
        'widget_enabled' => 'boolean',
        'widget_full_page' => 'boolean',
        'total_tokens_used' => 'integer',
        'total_input_tokens' => 'integer',
        'total_output_tokens' => 'integer',
        'total_cost' => 'decimal:6',
        'total_conversations' => 'integer',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ProjectSettingFactory::new();
    }
}
