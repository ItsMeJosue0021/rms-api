<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function logAdminAuthActivity(User $user, string $action, ?Request $request = null, array $metadata = []): void
    {
        if (! $user->hasRole([User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])) {
            return;
        }

        ActivityLog::query()->create([
            'actor_type' => $user->getMorphClass(),
            'actor_id' => $user->getKey(),
            'action' => $action,
            'metadata' => $metadata,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}

