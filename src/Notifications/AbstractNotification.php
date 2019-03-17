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

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications;

use Herpaderpaldent\Seat\SeatNotifications\Exceptions\UnknownDriverException;
use Herpaderpaldent\Seat\SeatNotifications\Models\NotificationSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Seat\Web\Models\Group;

/**
 * Class AbstractNotification.
 * @package Herpaderpaldent\Seat\SeatNotifications\Notifications
 */
abstract class AbstractNotification extends Notification implements INotification, ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue;

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * AbstractNotification constructor.
     */
    public function __construct()
    {
        $this->queue = 'high';
        $this->connection = 'redis';
    }

    /**
     * @param Group $group
     * @return mixed
     */
    public function getMainCharacter(Group $group)
    {
        $main_character = $group->main_character;

        if (is_null($main_character)) {
            logger()->warning('Group has no main character set. Attempt to make assignation based on first attached character.', [
                'group_id' => $group->id,
            ]);
            $main_character = $group->users->first()->character;
        }

        return $main_character;
    }

    /**
     * Assign this job a tag so that Horizon can categorize and allow
     * for specific tags to be monitored.
     *
     * If a job specifies the tags property, that is added.
     *
     * @return array
     */
    public function tags(): array
    {
        array_push($this->tags, 'seatnotifications');

        return $this->tags;
    }

    /**
     * TODO : is it an helper ?
     * Return the driver class which is related to the requested ID.
     * @param $driver_id string
     * @return string
     */
    final public static function getDriver(string $driver_id): string
    {
        $config_key = sprintf('services.seat-notification-channel.%s', $driver_id);

        return config($config_key);
    }

    /**
     * TODO : is it an helper ?
     * Determine if a notification has been subscribed for a specific driver.
     *
     * @param string $driver
     * @param bool $is_public
     *
     * @return bool
     */
    final public static function isSubscribed(string $driver_id): bool
    {
        return NotificationSubscription::where('notification', get_called_class())
            ->get()
            ->filter(function ($notification) use ($driver_id) {

                // check if the requested driver_id match with the recipient attached to this subscription
                return $notification->recipient->driver_id === $driver_id;
            })
            ->isNotEmpty();
    }

    /**
     * Return the class list of providers implementation.
     * @return array
     * @example
     * ```
     * [
     *   'provider_a' => ProviderANotificationImplementationClass,
     *   'provider_b' => ProviderBNotificationImplementationClass,
     * ]
     * ```
     */
    final public static function getDiversImplementations(): array
    {
        // build the configuration key related to the notification
        $config_key = sprintf('services.seat-notification.%s', get_called_class());

        return config($config_key, []);
    }

    /**
     * Return the class of a specific provider implementation.
     *
     * @param string $provider
     *
     * @return string
     * @throws \Herpaderpaldent\Seat\SeatNotifications\Exceptions\UnknownDriverException
     * @example
     * ```
     *   ProviderANotificationImplementationClass
     * ```
     */
    final public static function getDriverImplementation(string $provider): string
    {
        $available_drivers = self::getDiversImplementations();

        if (! array_key_exists($provider, $available_drivers))
            throw new UnknownDriverException($provider);

        return $available_drivers[$provider];
    }

    /**
     * @return array
     */
    public static function getFilters(): ?string
    {
        return null;
    }
}
