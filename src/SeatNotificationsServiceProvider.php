<?php
/**
 * MIT License.
 *
 * Copyright (c) 2019. Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Herpaderpaldent\Seat\SeatNotifications;

use Herpaderpaldent\Seat\SeatNotifications\Caches\RedisRateLimitProvider;
use Herpaderpaldent\Seat\SeatNotifications\Commands\SeatNotificationsTest;
use Herpaderpaldent\Seat\SeatNotifications\Commands\UpdateKillmails;
use Herpaderpaldent\Seat\SeatNotifications\Observers\KillmailDetailObserver;
use Herpaderpaldent\Seat\SeatNotifications\Observers\RefreshTokenObserver;
use Illuminate\Support\Arr;
use JoliCode\Slack\ClientFactory;
use RestCord\DiscordClient;
use Seat\Eveapi\Models\Killmails\KillmailDetail;
use Seat\Eveapi\Models\RefreshToken;
use Seat\Services\AbstractSeatPlugin;

class SeatNotificationsServiceProvider extends AbstractSeatPlugin
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

        $this->addDiscordContainer();
        $this->addSlackContainer();

        KillmailDetail::observe(KillmailDetailObserver::class);
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
            UpdateKillmails::class,
        ]);
    }

    private function add_migrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');
    }

    private function addRoutes()
    {
        if (! $this->app->routesAreCached()) {
            include __DIR__ . '/Http/Routes/routes.php';
        }
    }

    private function addViews()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'seatnotifications');
    }

    private function addTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'seatnotifications');
    }

    private function addDiscordContainer()
    {
        // push discord client into container as singleton if token has been set
        $bot_token = setting('herpaderp.seatnotifications.discord.credentials.bot_token', true);

        if (! is_null($bot_token)) {
            $this->app->singleton('seatnotifications-discord', function () {
                return new DiscordClient([
                    'tokenType'         => 'Bot',
                    'token'             => setting('herpaderp.seatnotifications.discord.credentials.bot_token', true),
                    'rateLimitProvider' => new RedisRateLimitProvider(),
                ]);
            });
        }

        // bind discord alias to DiscordClient
        $this->app->alias('seatnotifications-discord', DiscordClient::class);
    }

    private function addSlackContainer()
    {
        // push slack client into container as singleton if token has been set
        $bot_token = setting('herpaderp.seatnotifications.slack.credentials.bot_access_token', true);

        if (! is_null($bot_token)) {
            $this->app->singleton('slack', function () {
                return (new ClientFactory)->create(setting('herpaderp.seatnotifications.slack.credentials.bot_access_token', true));
            });
        }

        // bind discord alias to SlackClient
        $this->app->alias('slack', ClientFactory::class);
    }

    /**
     * Merge the given configuration with the existing configuration.
     * https://medium.com/@koenhoeijmakers/properly-merging-configs-in-laravel-packages-a4209701746d.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);
        $this->app['config']->set($key, $this->mergeConfigs(require $path, $config));
    }

    /**
     * Merges the configs together and takes multi-dimensional arrays into account.
     * https://medium.com/@koenhoeijmakers/properly-merging-configs-in-laravel-packages-a4209701746d.
     *
     * @param  array  $original
     * @param  array  $merging
     * @return array
     */
    protected function mergeConfigs(array $original, array $merging)
    {
        $array = array_merge($original, $merging);

        foreach ($original as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            if (! Arr::exists($merging, $key)) {
                continue;
            }

            if (is_numeric($key)) {
                continue;
            }

            $array[$key] = $this->mergeConfigs($value, $merging[$key]);
        }

        return $array;
    }

    /**
     * Return the plugin public name as it should be displayed into settings.
     *
     * @return string
     */
    public function getName(): string
    {

        return 'seat-notifications';
    }

    /**
     * Return the plugin repository address.
     *
     * @return string
     */
    public function getPackageRepositoryUrl(): string
    {

        return 'https://github.com/herpaderpaldent/seat-notifications';
    }

    /**
     * Return the plugin technical name as published on package manager.
     *
     * @return string
     */
    public function getPackagistPackageName(): string
    {

        return 'seat-notifications';
    }

    /**
     * Return the plugin vendor tag as published on package manager.
     *
     * @return string
     */
    public function getPackagistVendorName(): string
    {

        return 'herpaderpaldent';
    }

    /**
     * Return the plugin installed version.
     *
     * @return string
     */
    public function getVersion(): string
    {

        return config('seatnotifications.config.version');
    }

    /**
     * Return an URI to a CHANGELOG.md file or an API path which will be providing changelog history.
     *
     * @return string|null
     */
    public function getChangelogUri(): ?string
    {

        return 'https://raw.githubusercontent.com/herpaderpaldent/seat-notifications/master/CHANGELOG.md';
    }
}
