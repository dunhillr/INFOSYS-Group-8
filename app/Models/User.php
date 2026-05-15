<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'user_type',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function productions(){ return $this->hasMany(Production::class); }
    public function sales(){ return $this->hasMany(Sale::class); }
    public function notifications(){ return $this->hasMany(SystemNotification::class); }
    public function logs(){ return $this->hasMany(UserLog::class); }
    public function assignedDeliveries(){ return $this->hasMany(Delivery::class, 'assigned_by'); }
    public function completedDeliveries(){ return $this->hasMany(Delivery::class, 'delivered_by'); }
    public function driverDeliveries(){ return $this->hasMany(Delivery::class, 'driver_id'); }
    public function isDriver(): bool { return $this->user_type === 'driver'; }
    public function isOwner(): bool { return $this->user_type === 'owner'; }
    public function isEmployee(): bool { return $this->user_type === 'employee'; }
}