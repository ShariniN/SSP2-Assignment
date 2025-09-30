<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory; 

    protected $fillable = [
        'name',
        'description',
        'price',
        'discount_price',
        'stock_quantity',
        'category_id',
        'brand_id',
        'sku',
        'image',
        'specifications',
        'is_active',
        'is_featured'
    ];

    protected $casts = [
        'specifications' => 'json',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Get the effective price (discount price if available, otherwise regular price)
    public function getEffectivePriceAttribute()
    {
        return $this->discount_price && $this->discount_price < $this->price 
            ? $this->discount_price 
            : $this->price;
    }

    // Check if product is on sale
    public function getIsOnSaleAttribute()
    {
        return $this->discount_price && $this->discount_price < $this->price;
    }

    // Get savings amount
    public function getSavingsAttribute()
    {
        return $this->is_on_sale ? $this->price - $this->discount_price : 0;
    }

    // Get savings percentage
    public function getSavingsPercentageAttribute()
    {
        return $this->is_on_sale ? round((($this->price - $this->discount_price) / $this->price) * 100) : 0;
    }

    // Scope for active products
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for featured products
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Scope for in stock products
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // Scope for products on sale
    public function scopeOnSale($query)
    {
        return $query->whereNotNull('discount_price')
                    ->whereColumn('discount_price', '<', 'price');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'product_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'product_id');
    }
}