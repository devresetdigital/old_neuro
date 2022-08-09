<?php

namespace Custom\TrackingLogs\src\Providers;

use Illuminate\Support\ServiceProvider;

class TrackingLogsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . "/../../routes/api.php");
    }
}
