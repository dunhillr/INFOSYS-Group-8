<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $fillable = ['user_id','type','title','message','is_read'];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }

    public function user(){ return $this->belongsTo(User::class); }

    /**
     * Helper method to send a notification to all active users.
     */
    public static function notifyUsers(string $type, string $title, string $message): void
    {
        $users = User::where('is_active', true)->get();
        foreach ($users as $user) {
            self::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
            ]);
        }
    }
}