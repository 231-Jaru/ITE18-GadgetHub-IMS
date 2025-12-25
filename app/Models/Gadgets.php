<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gadgets extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gadgets';
    protected $primaryKey = 'GadgetID';

    protected $fillable = [
        'GadgetName',
        'CategoryID',
        'BrandID',
        'ReorderPoint',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'CategoryID', 'CategoryID');
    }

    public function brand()
    {
        return $this->belongsTo(Brands::class, 'BrandID', 'BrandID');
    }

    public function stocks()
    {
        return $this->hasMany(Stocks::class, 'GadgetID', 'GadgetID');
    }

    public function transactions()
    {
        return $this->hasMany(Transactions::class, 'GadgetID', 'GadgetID');
    }
}
