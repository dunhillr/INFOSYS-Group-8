<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id','customer_id','vehicle_id','driver_id','destination','delivery_date','delivery_time','status','assigned_by','delivered_by','notes','proof_of_delivery','is_opened'];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'is_opened' => 'boolean',
        ];
    }

    public function sale(){ return $this->belongsTo(Sale::class); }
    public function customer(){ return $this->belongsTo(Customer::class); }
    public function vehicle(){ return $this->belongsTo(Vehicle::class); }
    public function assigner(){ return $this->belongsTo(User::class, 'assigned_by'); }
    public function deliverer(){ return $this->belongsTo(User::class, 'delivered_by'); }
    public function driver(){ return $this->belongsTo(User::class, 'driver_id'); }
    public function logs(){ return $this->hasMany(DeliveryLog::class)->latest(); }
}