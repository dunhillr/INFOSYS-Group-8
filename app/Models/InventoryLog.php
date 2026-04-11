<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = ['inventory_id','reference_type','reference_id','movement_type','quantity','stock_before','stock_after','remarks','created_by'];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:2', 'stock_before' => 'decimal:2', 'stock_after' => 'decimal:2'];
    }

    public function inventory(){ return $this->belongsTo(Inventory::class); }
    public function creator(){ return $this->belongsTo(User::class, 'created_by'); }
}