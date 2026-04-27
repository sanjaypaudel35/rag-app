<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Sanjay\Ragbot\Database\Factories\ChunkFactory;
use Sanjay\Ragbot\Models\Traits\BelongsToProject;

/**
 * Model representing a document chunk.
 *
 * @property string $id
 * @property string $project_id
 * @property string $document_id
 * @property string $content
 * @property int $chunk_index
 * @property int|null $token_count
 */
class Chunk extends Model
{
    use HasUuids;
    use HasFactory;
    use BelongsToProject;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "rag_document_chunks";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "project_id",
        "document_id",
        "content",
        "chunk_index",
        "token_count",
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ChunkFactory::new();
    }

    /**
     * Get the document that owns the chunk.
     *
     * @return BelongsTo<Document, $this>
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the embedding associated with the chunk.
     *
     * @return HasOne<Embedding, $this>
     */
    public function embedding(): HasOne
    {
        return $this->hasOne(Embedding::class);
    }
}
