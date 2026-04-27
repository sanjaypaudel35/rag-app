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
 * @property VectorStore $vector_store
 * @property bool $widget_enabled
 */
class ProjectSetting extends Model
{
    use HasUuids;
    use HasFactory;
    use BelongsToProject;

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
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ProjectSettingFactory::new();
    }
}
