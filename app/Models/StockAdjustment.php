<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $table = 'stock_adjustments';
    protected $primaryKey = 'AdjustmentID';

    protected $fillable = [
        'GadgetID',
        'StockID',
        'AdminID',
        'AdjustmentType',
        'QuantityBefore',
        'QuantityAfter',
        'QuantityChanged',
        'Reason',
        'Notes',
        'AdjustmentDate',
    ];

    protected $casts = [
        'AdjustmentDate' => 'datetime',
    ];

    public function gadget()
    {
        return $this->belongsTo(Gadgets::class, 'GadgetID', 'GadgetID');
    }

    public function stock()
    {
        return $this->belongsTo(Stocks::class, 'StockID', 'StockID');
    }

    public function admin()
    {
        return $this->belongsTo(Admins::class, 'AdminID', 'AdminID');
    }
}
