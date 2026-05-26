<?php

namespace App\Observers;

use App\Models\Alphabet;
use App\Services\AlphabetService;
use App\Support\Cache\DashboardCache;

class AlphabetObserver
{
    public function saved(Alphabet $alphabet): void
    {
        AlphabetService::clearAlphabetCache();
        DashboardCache::forgetAdminStats();
    }

    public function deleted(Alphabet $alphabet): void
    {
        AlphabetService::clearAlphabetCache();
        DashboardCache::forgetAdminStats();
    }
}
