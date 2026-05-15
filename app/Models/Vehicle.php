<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = ['vehicle_name','plate_number','capacity','status'];

    protected function casts(): array
    {
        return ['capacity' => 'decimal:2'];
    }

    public function deliveries(){ return $this->hasMany(Delivery::class); }
    public function sales(){ return $this->hasMany(Sale::class); }
}