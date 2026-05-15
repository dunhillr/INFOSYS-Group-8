<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','current_stock','low_stock_threshold','updated_by'];

    protected function casts(): array
    {
        return ['current_stock' => 'decimal:2', 'low_stock_threshold' => 'decimal:2'];
    }

    public function product(){ return $this->belongsTo(Product::class); }
    public function logs(){ return $this->hasMany(InventoryLog::class); }
    public function updater(){ return $this->belongsTo(User::class, 'updated_by'); }
}