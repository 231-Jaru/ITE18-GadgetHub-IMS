<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $primaryKey = 'TransactionID';

    protected $fillable = [
        'GadgetID',
        'AdminID',
        'StockID',
        'TransactionType',
        'Quantity',
        'TransactionDate',
    ];

    protected $casts = [
        'TransactionDate' => 'datetime',
    ];

    public function gadget()
    {
        return $this->belongsTo(Gadgets::class, 'GadgetID', 'GadgetID');
    }

    public function admin()
    {
        return $this->belongsTo(Admins::class, 'AdminID', 'AdminID');
    }

    public function stock()
    {
        return $this->belongsTo(Stocks::class, 'StockID', 'StockID');
    }
}
