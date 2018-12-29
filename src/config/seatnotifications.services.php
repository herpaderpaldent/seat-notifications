<?php

return [

    'seat-notification-channel' => [
        'discord'   => Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Discord\DiscordNotificationChannel::class,
        'slack'     => Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Slack\SlackNotificationChannelController::class,
    ],
    'seat-notification' => [
        'refresh_token' => Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications\RefreshTokenController::class,
    ],

];
