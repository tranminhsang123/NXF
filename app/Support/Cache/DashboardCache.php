<?php

namespace App\Support\Cache;

use Illuminate\Support\Facades\Cache;

final class DashboardCache
{
    public static function forgetAdminStats(): void
    {
        Cache::forget('admin:dashboard:stats');
    }
}
