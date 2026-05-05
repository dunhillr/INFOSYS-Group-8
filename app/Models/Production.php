<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = ['production_date','batch_reference','product_id','quantity_produced','remarks','user_id','parent_product_id','parent_quantity_used'];

    protected function casts(): array
    {
        return [
            'production_date'      => 'date',
            'quantity_produced'    => 'decimal:2',
            'parent_quantity_used' => 'decimal:2',
        ];
    }

    public function product(){ return $this->belongsTo(Product::class); }
    public function user(){ return $this->belongsTo(User::class); }

    /** The raw material (parent product) consumed during this production run */
    public function parentProduct(){ return $this->belongsTo(Product::class, 'parent_product_id'); }
}