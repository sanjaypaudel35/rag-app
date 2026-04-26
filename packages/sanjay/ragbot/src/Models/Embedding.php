<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Get the project that owns the embedding.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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
