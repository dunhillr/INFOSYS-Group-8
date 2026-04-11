<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = ['production_date','batch_reference','quantity_produced','remarks','user_id'];

    protected function casts(): array
    {
        return ['production_date' => 'date', 'quantity_produced' => 'decimal:2'];
    }

    public function user(){ return $this->belongsTo(User::class); }
}