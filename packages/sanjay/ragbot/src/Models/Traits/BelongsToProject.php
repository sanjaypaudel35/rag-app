<?php

namespace Sanjay\Ragbot\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Sanjay\Ragbot\Models\Project;

trait BelongsToProject
{
    /**
     * Boot the trait to add the global scope.
     */
    protected static function bootBelongsToProject(): void
    {
        static::addGlobalScope('project', function (Builder $builder) {
            if (app()->bound('ragbot.project')) {
                /** @var Project $project */
                $project = app('ragbot.project');
                $builder->where('project_id', $project->id);
            }
        });

        static::creating(function (Model $model) {
            if (app()->bound('ragbot.project') && empty($model->project_id)) {
                /** @var Project $project */
                $project = app('ragbot.project');
                $model->project_id = $project->id;
            }
        });
    }

    /**
     * Get the project that owns the model.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
