<?php

namespace App\Modules\Monitoring;

use App\Modules\Monitoring\Commands\CheckDomains;
use Illuminate\Support\ServiceProvider;

class MonitoringServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckDomains::class,
            ]);
        }
    }
}
