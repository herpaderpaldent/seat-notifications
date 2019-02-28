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

    /**
     * @return array
     */
    final public static function getFilters(): ?string
    {
        return 'corporations';
    }
}
