<?php

return [
    // config/services.php
    'discord' => [
        'client_id' => env('DISCORD_KEY'),
        'client_secret' => env('DISCORD_SECRET'),
        'redirect' => env('DISCORD_REDIRECT_URI'),
        'token' => env('DISCORD_BOT_TOKEN'),
    ],

    'laravel-notification-channel' => [
        'discord' => Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordNotificationChannel::class,
    ]


];