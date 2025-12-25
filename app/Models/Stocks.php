<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\CurrencyHelper;

class Stocks extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stocks';
    protected $primaryKey = 'StockID';

    protected $fillable = [
        'GadgetID',
        'SupplierID',
        'QuantityAdded',
        'CostPrice',
        'PurchaseDate',
    ];

    protected $casts = [
        'PurchaseDate' => 'datetime',
    ];

    /**
     * Get Cost Price in PHP (converted from USD if needed)
     */
    public function getCostPricePhpAttribute(): float
    {
        return CurrencyHelper::getPhpPrice($this->CostPrice);
    }

    public function gadget()
    {
        return $this->belongsTo(Gadgets::class, 'GadgetID', 'GadgetID');
    }

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'SupplierID', 'SupplierID');
    }

    public function transactions()
    {
        return $this->hasMany(Transactions::class, 'StockID', 'StockID');
    }
}
