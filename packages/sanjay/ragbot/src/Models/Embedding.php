<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sanjay\Ragbot\Database\Factories\EmbeddingFactory;
use Sanjay\Ragbot\Models\Traits\BelongsToProject;

/**
 * Model representing a vector embedding.
 *
 * @property string $id
 * @property string $project_id
 * @property string $chunk_id
 * @property array $vector
 */
class Embedding extends Model
{
    use BelongsToProject;
    use HasFactory;
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rag_embeddings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'chunk_id',
        'vector',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'vector' => 'array',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return EmbeddingFactory::new();
    }

    /**
     * Get the chunk that owns the embedding.
     *
     * @return BelongsTo<Chunk, $this>
     */
    public function chunk(): BelongsTo
    {
        return $this->belongsTo(Chunk::class);
    }
}
