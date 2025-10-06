<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'total'  // Changed from 'session_id' to match your actual table
    ];
    
    /**
     * Get the cart items for this cart
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
    
    /**
     * Get the user that owns the cart 
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the total quantity of items in the cart
     */
    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }
    
    /**
     * Get the total price of all items in the cart
     */
    public function getTotalPriceAttribute()
    {
        return $this->items->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
    }
}