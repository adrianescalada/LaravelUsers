<?php

namespace wdna\users;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;

class UsersServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->defineMiddleware($router);
        
        /* MIGRACIONES */
        $this->loadMigrationsFrom([__DIR__.'/../database/migrations']);

        /* ROUTES */
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        /* VIEWS */
        $this->loadViewsFrom(__DIR__.'/../views', 'users');
        //$this->publishes([__DIR__.'/../views' => resource_path('views/vendor/customers')], 'views');
    }

    private function defineMiddleware($router)
    {
        foreach ($this->middlewares as $name => $class) {
            if ( version_compare(app()->version(), '5.4.0') >= 0 ) {
                $router->aliasMiddleware($name, $class);
            } else {
                $router->middleware($name, $class);
            }
        }
    }

}