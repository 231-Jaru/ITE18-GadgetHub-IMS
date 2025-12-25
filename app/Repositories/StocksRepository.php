<?php

namespace App\Repositories;

use App\Models\Stocks;
use App\Models\Gadgets;
use Illuminate\Support\Facades\DB;

class StocksRepository
{
    protected $model;

    public function __construct(Stocks $model)
    {
        $this->model = $model;
    }

    /**
     * Get all stocks with relationships
     * 
     * @param array $relationships
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllWithRelations(array $relationships = ['gadget.category', 'gadget.brand', 'supplier'], $perPage = 15)
    {
        return $this->model
            ->with($relationships)
            ->paginate($perPage);
    }

    /**
     * Get stock by ID with relationships
     * 
     * @param int $id
     * @param array $relationships
     * @return Stocks|null
     */
    public function findWithRelations($id, array $relationships = ['gadget.category', 'gadget.brand', 'gadget.stocks', 'supplier'])
    {
        return $this->model
            ->with($relationships)
            ->find($id);
    }

    /**
     * Create a new stock entry
     * 
     * @param array $data
     * @return Stocks
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update stock
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
        $stock = $this->model->find($id);
        
        if (!$stock) {
            return false;
        }

        return $stock->update($data);
    }

    /**
     * Delete stock
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stock = $this->model->find($id);
        
        if (!$stock) {
            return false;
        }

        return $stock->delete();
    }

    /**
     * Get low stock items
     * 
     * @param int $threshold
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowStock($threshold = 10, $limit = 5, array $relationships = ['gadget'])
    {
        return $this->model
            ->with($relationships)
            ->where('QuantityAdded', '<', $threshold)
            ->whereHas('gadget')
            ->orderBy('QuantityAdded', 'asc')
            ->limit($limit)
            ->get()
            ->filter(function($stock) {
                return $stock->gadget !== null;
            })
            ->values();
    }

    /**
     * Get out of stock items
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOutOfStock(array $relationships = ['gadget'])
    {
        return $this->model
            ->with($relationships)
            ->where('QuantityAdded', 0)
            ->whereHas('gadget')
            ->get();
    }

    /**
     * Get gadgets without stock
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGadgetsWithoutStock()
    {
        return Gadgets::with(['category', 'brand', 'stocks'])
            ->get()
            ->filter(function($gadget) {
                return $gadget->stocks->isEmpty();
            });
    }
}

