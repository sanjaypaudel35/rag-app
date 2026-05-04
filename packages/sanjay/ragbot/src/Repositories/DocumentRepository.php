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
     */
    public function __construct(Document $model)
    {
        parent::__construct($model);
    }

    /**
     * Find documents by status.
     *
     * @return Collection<int, Document>
     */
    public function findByStatus(string $status): Collection
    {
        return $this->model
            ->where('status', $status)
            ->get();
    }

    /**
     * Update document status.
     */
    public function updateStatus(string $id, string $status, ?string $error = null): bool
    {
        $data = ['status' => $status];

        if ($error !== null) {
            $data['error_message'] = $error;
        }

        $this->update($id, $data);

        return true;
    }
}
