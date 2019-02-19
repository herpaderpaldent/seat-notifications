<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken;

use Herpaderpaldent\Seat\SeatNotifications\Notifications\AbstractNotification;

/**
 * Class AbstractRefreshTokenNotification.
 * @package Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken
 */
abstract class AbstractRefreshTokenNotification extends AbstractNotification
{
    /**
     * @return string
     */
    final public static function getTitle(): string
    {
        return 'Refresh Token Deletion';
    }

    /**
     * @return string
     */
    final public static function getDescription(): string
    {
        return 'Receive a notification as soon a deletion is detected.';
    }

    /**
     * unique notificiation string.
     *
     * @return string
     */
    final public static function getName(): string
    {
        return 'refresh_token';
    }

    /**
     * @return bool
     */
    final public static function isPublic(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    final public static function isPersonal(): bool
    {
        return true;
    }
}
