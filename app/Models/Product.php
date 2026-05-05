<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['product_name','product_code','description','default_price','is_active','parent_product_id','weight_kg'];

    protected function casts(): array
    {
        return [
            'default_price' => 'decimal:2', 
            'is_active' => 'boolean',
            'weight_kg' => 'decimal:2',
        ];
    }

    public function inventory(){ return $this->hasOne(Inventory::class); }
    public function sales(){ return $this->hasMany(Sale::class); }

    /** The raw material this product is derived from (e.g. Block Ice for Crushed Ice) */
    public function parentProduct(){ return $this->belongsTo(Product::class, 'parent_product_id'); }

    /** Products that are derived from this product */
    public function childProducts(){ return $this->hasMany(Product::class, 'parent_product_id'); }
}