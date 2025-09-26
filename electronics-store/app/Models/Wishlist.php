<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Wishlist extends Model
{
    protected $connection = 'mongodb'; 
    protected $collection = 'wishlists'; 

    protected $fillable = [
        'user_id',
        'product_id',
        'product_snapshot',
        'added_at',
    ];

    protected function casts(): array
    {
        return [
            'added_at' => 'datetime',
        ];
    }
}
