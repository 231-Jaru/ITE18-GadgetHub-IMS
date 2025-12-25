<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suppliers extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'SupplierID';

    protected $table = 'suppliers';
    protected $fillable = [
        'SupplierName',
        'ContactPerson',
        'Phone',
        'Email',
    ];

    public function stocks()
    {
        return $this->hasMany(Stocks::class, 'SupplierID', 'SupplierID');
    }
}
