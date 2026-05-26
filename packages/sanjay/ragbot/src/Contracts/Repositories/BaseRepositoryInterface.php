<?php

namespace Sanjay\Ragbot\Contracts\Repositories;

use Illuminate\Database\Eloquent\Builder;
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
     * @param  array<string>  $relationships
     */
    public function findById(string $id, array $relationships = []): Model;

    /**
     * Create a new model instance in the database.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model;

    /**
     * Update an existing model instance.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): Model;

    /**
     * Create or update a model instance.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $values
     */
    public function updateOrCreate(array $attributes, array $values = []): Model;

    /**
     * Find one model by attributes.
     *
     * @param  array<string, mixed>  $attributes
     * @return TModel|null
     */
    public function findOneBy(array $attributes): ?Model;

    /**
     * Find one model using a closure for custom query constraints.
     *
     * @param  callable(Builder): void  $callback
     * @return TModel|null
     */
    public function findOne(callable $callback): ?Model;

    /**
     * Delete a model instance from the database.
     */
    public function delete(string $id): void;

    /**
     * Get all records.
     */
    public function all(array $relationships = []): Collection;
}
