<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications;

use Herpaderpaldent\Seat\SeatNotifications\Drivers\INotificationDriver;

/**
 * Class AbstractNotification
 * @package Herpaderpaldent\Seat\SeatNotifications\Notifications
 */
abstract class AbstractNotification
{
    /**
     * TODO : is it an helper ?
     * Return the driver class which is related to the requested ID
     * @param $driver_id string
     * @return string
     */
    final public static function getDriver(string $driver_id): string
    {
        $config_key = sprintf('services.seat-notification-channel.%s', $driver_id);

        return config($config_key);
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
