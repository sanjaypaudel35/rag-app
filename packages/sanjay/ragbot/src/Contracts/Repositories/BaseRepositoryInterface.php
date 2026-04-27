<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
interface BaseRepositoryInterface
{
    /**
     * Find a model by its unique identifier.
     *
     * @param string $id
     * @param array $relationships
     * @return Model
     */
    public function findById(string $id, array $relationships = []): Model;

    /**
     * Create a new model instance in the database.
     *
     * @param array<string, mixed> $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update an existing model instance.
     *
     * @param string $id
     * @param array<string, mixed> $data
     * @return TModel
     */
    public function update(string $id, array $data): Model;

      /**
     * Delete a model instance from the database.
     *
     * @param string $id
     * @return void
     */
    public function delete(string $id): void;

    /**
     * Get all records.
     *
     * @param array $relationships
     * @return Collection
     */
    public function all(array $relationships = []): Collection;
}
