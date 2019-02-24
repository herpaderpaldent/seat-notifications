<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications;

use Herpaderpaldent\Seat\SeatNotifications\Models\SeatNotification;

/**
 * Class AbstractNotification.
 * @package Herpaderpaldent\Seat\SeatNotifications\Notifications
 */
abstract class AbstractNotification
{
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
     * @param string $driver_id
     *
     * @return bool
     */
    final public static function isSubscribed(string $driver_id): bool
    {

        return SeatNotification::where('name', get_called_class())
            ->get()
            ->filter(function ($notification) use ($driver_id) {

                if(is_null($driver_id))
                    return $notification->recipients->is_channel;

                return $notification->channel_id === $driver_id;
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
}
