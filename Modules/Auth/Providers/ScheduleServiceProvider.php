<?php

namespace Modules\Auth\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            //            $schedule->command('sanctum:prune-expired --hours=24')->daily();
            //            $schedule->command('auth:clear-resets')->hourly();
            //            $schedule->command('auth:clear-unverified-users')->daily();
            //            $schedule->command('media-library:clean')->daily();
        });
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        //
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
