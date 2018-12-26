<?php

return [

    'seat-notification-channel' => [
        'discord' => Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordNotificationChannel::class,
    ],
    'seat-notification' => [
        'refresh_token' => Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications\RefreshTokenController::class,
    ]


];