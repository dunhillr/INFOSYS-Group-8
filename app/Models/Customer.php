<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['customer_name','customer_contact','customer_address','customer_type','notes'];

    public function sales(){ return $this->hasMany(Sale::class); }
    public function deliveries(){ return $this->hasMany(Delivery::class); }
}