<?php

namespace wdna\users;

use Illuminate\Support\ServiceProvider;

class UsersServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
                /* MIGRACIONES */
        $this->loadMigrationsFrom([__DIR__.'/../database/migrations']);

        /* ROUTES */
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        /* VIEWS */
        $this->loadViewsFrom(__DIR__.'/../views', 'users');
        //$this->publishes([__DIR__.'/../views' => resource_path('views/vendor/customers')], 'views');
    }

}