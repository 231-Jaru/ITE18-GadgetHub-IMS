<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders';
    protected $primaryKey = 'PurchaseOrderID';

    protected $fillable = [
        'PONumber',
        'SupplierID',
        'AdminID',
        'Status',
        'TotalAmount',
        'OrderDate',
        'ExpectedDeliveryDate',
        'ReceivedDate',
        'Notes',
    ];

    protected $casts = [
        'OrderDate' => 'date',
        'ExpectedDeliveryDate' => 'date',
        'ReceivedDate' => 'date',
        'TotalAmount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'SupplierID', 'SupplierID');
    }

    public function admin()
    {
        return $this->belongsTo(Admins::class, 'AdminID', 'AdminID');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'PurchaseOrderID', 'PurchaseOrderID');
    }
}
