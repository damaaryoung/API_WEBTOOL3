<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
// use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // config(['app.locale' => 'id']);
        // \Carbon\Carbon::setLocale('id');
        //  Schema::defaultStringLength(191);
        Carbon::serializeUsing(function ($carbon) {
            return $carbon->format('m-d-Y H:i:s');
        });
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Configs to Redis
        // $this->app->configure('database');

        // Enable queues
        // $this->app->make('queue');
    }
}
