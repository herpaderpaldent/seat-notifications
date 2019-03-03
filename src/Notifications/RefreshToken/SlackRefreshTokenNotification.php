<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken;

use Exception;

/**
 * Class SlackRefreshTokenNotification.
 * @package Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken
 */
class SlackRefreshTokenNotification extends AbstractRefreshTokenNotification
{
    /**
     * @param $notifiable
     * @throws Exception
     */
    public function via($notifiable)
    {
        throw new Exception('Not implemented function.');
    }
}
