<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_items';
    protected $primaryKey = 'POItemID';

    protected $fillable = [
        'PurchaseOrderID',
        'GadgetID',
        'Quantity',
        'UnitCost',
        'TotalCost',
        'QuantityReceived',
    ];

    protected $casts = [
        'UnitCost' => 'decimal:2',
        'TotalCost' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'PurchaseOrderID', 'PurchaseOrderID');
    }

    public function gadget()
    {
        return $this->belongsTo(Gadgets::class, 'GadgetID', 'GadgetID');
    }
}
