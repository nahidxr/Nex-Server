<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
//    public function boot()
//    {
//        if (env('REDIRECT_HTTPS')) {
//            URL::forceScheme('https');
//        }
//    }

    public function boot()
    {
        Paginator::useBootstrap();
    }
}
