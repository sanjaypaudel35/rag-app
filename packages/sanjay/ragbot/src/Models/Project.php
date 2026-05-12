<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Sanjay\Ragbot\Database\Factories\ProjectFactory;

/**
 * Model representing a Project (Tenant).
 *
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $api_key
 * @property bool $is_active
 */
class Project extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rag_projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'logo',
        'slug',
        'api_key',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ProjectFactory::new();
    }

    /**
     * Get the settings associated with the project.
     *
     * @return HasOne<ProjectSetting, $this>
     */
    public function settings(): HasOne
    {
        return $this->hasOne(ProjectSetting::class);
    }

    /**
     * Get the users associated with the project.
     *
     * @return HasMany<RagbotUser, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(RagbotUser::class);
    }

    /**
     * Get the documents associated with the project.
     *
     * @return HasMany<Document, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the conversations associated with the project.
     *
     * @return HasMany<Conversation, $this>
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Get the chatbots associated with the project.
     *
     * @return HasMany<Chatbot, $this>
     */
    public function chatbots(): HasMany
    {
        return $this->hasMany(Chatbot::class);
    }
}
