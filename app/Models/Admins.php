<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admins extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $table = 'admins';
    protected $primaryKey = 'AdminID';

    protected $fillable = [
        'Username',
        'PasswordHash',
        'Role',
    ];

    protected $hidden = [
        'PasswordHash',
    ];

    public function transactions()
    {
        return $this->hasMany(Transactions::class, 'AdminID', 'AdminID');
    }

    /**
     * Get the password attribute name for authentication
     */
    public function getAuthPassword()
    {
        return $this->PasswordHash;
    }

    /**
     * Get the name of the unique identifier for the user
     */
    public function getAuthIdentifierName()
    {
        return 'Username';
    }
}

