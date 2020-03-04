<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Configs to Redis
        $this->app->configure('database');

        // Enable queues
        $this->app->make('queue');
    }
}
