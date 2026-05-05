<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property float $quantity 
 */

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['sale_number', 'product_id','customer_id','vehicle_id','sale_date','sale_type','delivery_type','quantity','unit_price','delivery_fee','discount_type','discount_amount','total_amount','payment_status','amount_paid','amount_tendered','change_amount','balance_due','payment_method','notes','user_id'];

    protected function casts(): array
    {
        return ['sale_date' => 'datetime', 'quantity' => 'decimal:2', 'unit_price' => 'decimal:2', 'delivery_fee' => 'decimal:2', 'discount_amount' => 'decimal:2', 'total_amount' => 'decimal:2', 'amount_paid' => 'decimal:2', 'amount_tendered' => 'decimal:2', 'change_amount' => 'decimal:2', 'balance_due' => 'decimal:2'];
    }

    public function product(){ return $this->belongsTo(Product::class); }
    public function customer(){ return $this->belongsTo(Customer::class); }
    public function vehicle(){ return $this->belongsTo(Vehicle::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function delivery(){ return $this->hasOne(Delivery::class); }
    public function saleItems(){ return $this->hasMany(SaleItem::class); }
}