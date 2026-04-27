<?php

namespace Sanjay\Ragbot\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface;
use Sanjay\Ragbot\Models\Document;

/**
 * Repository for managing Document entities.
 */
class DocumentRepository extends BaseRepository implements DocumentRepositoryInterface
{
    /**
     * Create a new document repository instance.
     *
     * @param Document $model
     */
    public function __construct(Document $model)
    {
        parent::__construct($model);
    }

    /**
     * Find documents by status.
     *
     * @param string $status
     * @return Collection<int, Document>
     */
    public function findByStatus(string $status): Collection
    {
        return $this->model
            ->where("status", $status)
            ->get();
    }

    /**
     * Update document status.
     *
     * @param string $id
     * @param string $status
     * @param string|null $error
     * @return bool
     */
    public function updateStatus(string $id, string $status, ?string $error = null): bool
    {
        $data = ["status" => $status];

        if ($error !== null) {
            $data["error_message"] = $error;
        }

        return $this->update($id, $data);
    }
}
