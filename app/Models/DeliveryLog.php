<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryLog extends Model
{
    protected $fillable = ['delivery_id', 'status', 'notes'];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}
