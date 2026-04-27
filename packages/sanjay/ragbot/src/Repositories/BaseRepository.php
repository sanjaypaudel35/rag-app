<?php

namespace Sanjay\Ragbot\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Sanjay\Ragbot\Contracts\Repositories\BaseRepositoryInterface;

/**
 * Base repository class providing common data access patterns.
 *
 * @template TModel of Model
 * @implements BaseRepositoryInterface<TModel>
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * Create a new repository instance.
     *
     * @param TModel $model
     */
    public function __construct(protected Model $model)
    {
    }

    /**
     * Find a model by its unique identifier.
     *
     * @param string $id
     * @param array $relationships
     * @return TModel
     *
     * @throws ModelNotFoundException
     */
    public function findById(string $id, array $relationships = []): Model
    {
        return $this->model->with($relationships)->findOrFail($id);
    }

    /**
     * Create a new model instance in the database.
     *
     * @param array<string, mixed> $data
     * @return TModel
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing model instance.
     *
     * @param string $id
     * @param array<string, mixed> $data
     * @return TModel
     */
    public function update(string $id, array $data): Model
    {
        try {
            $record = $this->findById($id);
            $record->update($data);
            return $record;
        } catch (ModelNotFoundException $e) {
            throw $e;
        }
    }

    /**
     * Delete a model instance from the database.
     *
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        try {
            $record = $this->findById($id);
            $record->delete();
        } catch (ModelNotFoundException $e) {
            throw $e;
        }
    }

    /**
     * Get all records.
     *
     * @param array $relationships
     * @return Collection<int, TModel>
     */
    public function all(array $relationships = []): Collection
    {
        return $this->model->with($relationships)->get();
    }
}
