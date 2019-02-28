<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications;

use Herpaderpaldent\Seat\SeatNotifications\Models\NotificationSubscription;

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
     * @param string $driver
     * @param bool $is_public
     *
     * @return bool
     */
    final public static function isSubscribed(string $driver, bool $is_public = true): bool
    {
        return NotificationSubscription::where('notification', get_called_class())
            ->get()
            ->filter(function ($notification) use ($driver, $is_public) {

                // check if the requested driver match with the recipient attached to this subscription
                if ($notification->recipient->driver !== $driver)
                    return false;

                // if the requested subscription must be public,
                // check if the current subscription is attached to a recipient without group_id
                if ($is_public)
                    return is_null($notification->recipient->group_id);

                // if the requested subscription must be private,
                // check if the current subscription is attached to a recipient with the currently authenticated user
                return $notification->recipient->group_id === auth()->user()->group_id;
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
     * @return array
     */
    public static function getFilters(): ?string
    {
        return null;
    }
}
