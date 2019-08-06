<?php
namespace Payment\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
class LocalPaymentServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
//        include __DIR__ . '/../Http/routes.php';
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'payment');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'payment');
    }
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php', 'paymentmethods'
        );
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );
    }
}