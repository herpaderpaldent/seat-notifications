<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken;

/**
 * Class DiscordRefreshTokenNotification.
 * @package Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken
 */
class DiscordRefreshTokenNotification extends AbstractRefreshTokenNotification
{
    /**
     * Determine if channel has personal notification setup.
     *
     * @return bool
     */
    public static function hasPersonalNotification() : bool
    {
        return true;
    }
}
