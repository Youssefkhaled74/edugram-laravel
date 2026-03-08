<?php

namespace Modules\ParentModule;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ParentModuleServiceProvider extends ServiceProvider
{
    /**
     * Module namespace.
     *
     * @var string
     */
    protected $namespace = 'Modules\ParentModule\Http\Controllers';

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerViews();
        $this->registerConfig();
        $this->registerMiddleware();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/config.php', 'parentmodule'
        );
    }

    /**
     * Register routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require __DIR__ . '/Routes/web.php';
        });
    }

    /**
     * Register migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }

    /**
     * Register views.
     *
     * @return void
     */
    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'parentmodule');
        
        $this->publishes([
            __DIR__ . '/Resources/views' => resource_path('views/vendor/parentmodule'),
        ], 'views');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/Config/config.php' => config_path('parentmodule.php'),
        ], 'config');
    }

    /**
     * Register middleware.
     *
     * @return void
     */
    protected function registerMiddleware()
    {
        $router = $this->app['router'];
        
        $router->aliasMiddleware('parent', \Modules\ParentModule\Http\Middleware\ParentMiddleware::class);
    }
}

