<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Sanjay\Ragbot\Enums\LlmProvider;
use Sanjay\Ragbot\Enums\VectorStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model representing Project-specific settings.
 *
 * @property string $id
 * @property string $project_id
 * @property LlmProvider $llm_provider
 * @property string|null $llm_api_key
 * @property string|null $llm_model
 * @property VectorStore $vector_store
 * @property bool $widget_enabled
 */
class ProjectSetting extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "rag_project_settings";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "project_id",
        "llm_provider",
        "llm_api_key",
        "llm_model",
        "vector_store",
        "widget_enabled",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "llm_provider" => LlmProvider::class,
        "vector_store" => VectorStore::class,
        "widget_enabled" => "boolean",
    ];

    /**
     * Get the project that owns the settings.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
