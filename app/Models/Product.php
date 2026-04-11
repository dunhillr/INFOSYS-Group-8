<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['product_name','product_code','description','default_price','is_active'];

    protected function casts(): array
    {
        return ['default_price' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function inventory(){ return $this->hasOne(Inventory::class); }
    public function sales(){ return $this->hasMany(Sale::class); }
}