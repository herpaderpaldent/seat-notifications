<?php

return [
    // config/services.php
    'discord' => [
        'token' => env('DISCORD_BOT_TOKEN'),
    ],

    'laravel-notification-channel' => [
        'discord' => Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordNotificationChannel::class,
    ]
];