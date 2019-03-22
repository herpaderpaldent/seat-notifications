<?php
/**
 * MIT License.
 *
 * Copyright (c) 2019. Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

return [
    'seat-notification-channel' => [
        //'discourse' => Herpaderpaldent\Seat\SeatNotifications\Drivers\DiscourseNotificationDriver::class,
        'discord' => Herpaderpaldent\Seat\SeatNotifications\Drivers\DiscordNotificationDriver::class,
        'slack'   => Herpaderpaldent\Seat\SeatNotifications\Drivers\SlackNotificationDriver::class,
    ],
    'seat-notification'         => [
        // notification => [provider => implementation]
        Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken\AbstractRefreshTokenNotification::class => [
            'discord' => Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken\DiscordRefreshTokenNotification::class,
            'slack'   => Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken\SlackRefreshTokenNotification::class,
        ],
        Herpaderpaldent\Seat\SeatNotifications\Notifications\KillMail\AbstractKillMailNotification::class         => [
            'discord' => Herpaderpaldent\Seat\SeatNotifications\Notifications\KillMail\DiscordKillMailNotification::class,
            'slack'   => Herpaderpaldent\Seat\SeatNotifications\Notifications\KillMail\SlackKillMailNotification::class,
        ],

    ],
];
