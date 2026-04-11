<?php

namespace App\Services;

use App\Models\UserLog;
use Illuminate\Http\Request;

class ActivityLogService
{
    public static function log(?int $userId, string $action, string $module, ?string $description = null, ?Request $request = null): void
    {
        UserLog::create([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}