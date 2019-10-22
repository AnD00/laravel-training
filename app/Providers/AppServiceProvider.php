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
        // Company
        $this->app->bind(
            \App\Repositories\Company\RepositoryInterface::class,
            \App\Repositories\Company\Repository::class,
        );
        // Job
        $this->app->bind(
            \App\Repositories\Job\RepositoryInterface::class,
            \App\Repositories\Job\Repository::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
