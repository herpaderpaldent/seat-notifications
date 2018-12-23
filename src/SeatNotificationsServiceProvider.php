<?php

namespace Herpaderpaldent\Seat\SeatNotifications;

use Herpaderpaldent\Seat\SeatNotifications\Commands\SeatNotificationsTest;
use Herpaderpaldent\Seat\SeatNotifications\Observers\RefreshTokenObserver;
use Illuminate\Support\ServiceProvider;
use Seat\Eveapi\Models\RefreshToken;

class SeatNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->addCommands();
        RefreshToken::observe(RefreshTokenObserver::class);

        $this->addRoutes();
        $this->addViews();
        $this->add_migrations();
        //$this->addTranslations();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/seatnotifications.permission.php', 'web.permissions');

        $this->mergeConfigFrom(
            __DIR__ . '/config/seatnotifications.services.php', 'services');

        $this->mergeConfigFrom(
            __DIR__ . '/config/seatnotifications.config.php', 'seatnotifications.config'
        );
        $this->mergeConfigFrom(
            __DIR__ . '/config/seatnotifications.sidebar.php', 'package.sidebar');
    }

    private function addCommands()
    {
        $this->commands([
            SeatNotificationsTest::class,
        ]);
    }

    private function add_migrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');
    }

    private function addRoutes()
    {
        if (!$this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }
    }

    private function addViews()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'seatnotifications');
    }

    private function addTranslations()
    {
        //$this->loadTranslationsFrom(__DIR__ . '/lang', 'seatgroups');
    }
}