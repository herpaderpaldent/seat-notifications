<?php


namespace Herpaderpaldent\Seat\SeatNotifications\Drivers;

/**
 * Class AbstractNotificationDriver.
 * @package Herpaderpaldent\Seat\SeatNotifications\Drivers
 */
abstract class AbstractNotificationDriver implements INotificationDriver
{

    /**
     * Determine if a channel is supporting private notifications.
     *
     * @return bool
     */
    public static function allowPersonalNotifications(): bool
    {
        return false;
    }

    /**
     * Determine if a channel is supporting public notifications.
     *
     * @return bool
     */
    public static function allowPublicNotifications(): bool
    {
        return false;
    }

    /**
     * Return channel_id of user.
     *
     * @return string
     */
    public static function getPrivateChannel(): ?string
    {
        return null;
    }

    /**
     * Return the route key which have to be used in a private notification registration flow.
     *
     * @return string
     */
    public static function getPrivateRegistrationRoute(): ?string
    {
        return null;
    }
}
