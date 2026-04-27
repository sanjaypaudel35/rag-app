<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sanjay\Ragbot\Database\Factories\DocumentFactory;
use Sanjay\Ragbot\Enums\DocumentStatus;
use Sanjay\Ragbot\Models\Traits\BelongsToProject;

/**
 * Model representing an uploaded document.
 *
 * @property string $id
 * @property string $project_id
 * @property string $name
 * @property string $file_path
 * @property string $mime_type
 * @property DocumentStatus $status
 * @property string|null $error_message
 */
class Document extends Model
{
    use HasUuids;
    use HasFactory;
    use BelongsToProject;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "rag_documents";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "project_id",
        "name",
        "file_path",
        "mime_type",
        "status",
        "error_message",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "status" => DocumentStatus::class,
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return DocumentFactory::new();
    }

    /**
     * Get the chunks associated with the document.
     *
     * @return HasMany<Chunk, $this>
     */
    public function chunks(): HasMany
    {
        return $this->hasMany(Chunk::class);
    }
}
