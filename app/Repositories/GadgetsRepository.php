<?php

namespace App\Repositories;

use App\Models\Gadgets;
use Illuminate\Support\Facades\DB;

class GadgetsRepository
{
    protected $model;

    public function __construct(Gadgets $model)
    {
        $this->model = $model;
    }

    /**
     * Get all gadgets with relationships
     * 
     * @param array $relationships
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllWithRelations(array $relationships = ['category', 'brand', 'stocks'], $perPage = 15)
    {
        return $this->model
            ->with($relationships)
            ->paginate($perPage);
    }

    /**
     * Get gadget by ID with relationships
     * 
     * @param int $id
     * @param array $relationships
     * @return Gadgets|null
     */
    public function findWithRelations($id, array $relationships = ['category', 'brand', 'stocks.supplier'])
    {
        return $this->model
            ->with($relationships)
            ->find($id);
    }

    /**
     * Create a new gadget
     * 
     * @param array $data
     * @return Gadgets
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update gadget
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
        $gadget = $this->model->find($id);
        
        if (!$gadget) {
            return false;
        }

        return $gadget->update($data);
    }

    /**
     * Delete gadget (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $gadget = $this->model->find($id);
        
        if (!$gadget) {
            return false;
        }

        return $gadget->delete();
    }

    /**
     * Get soft-deleted gadgets
     * 
     * @param array $relationships
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDeleted(array $relationships = ['category', 'brand'])
    {
        return $this->model
            ->onlyTrashed()
            ->with($relationships)
            ->get();
    }

    /**
     * Restore soft-deleted gadget
     * 
     * @param int $id
     * @return bool
     */
    public function restore($id)
    {
        $gadget = $this->model->withTrashed()->find($id);
        
        if (!$gadget || !$gadget->trashed()) {
            return false;
        }

        return $gadget->restore();
    }

    /**
     * Permanently delete gadget
     * 
     * @param int $id
     * @return bool
     */
    public function forceDelete($id)
    {
        $gadget = $this->model->withTrashed()->find($id);
        
        if (!$gadget || !$gadget->trashed()) {
            return false;
        }

        return $gadget->forceDelete();
    }
}

