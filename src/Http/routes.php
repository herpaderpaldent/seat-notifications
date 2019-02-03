<?php
/**
 * MIT License
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
 *
 */

Route::group([
    'namespace' => 'Herpaderpaldent\Seat\SeatNotifications\Http\Controllers',
    'prefix'    => 'seatnotifications',
    'middleware' => ['web', 'auth', 'bouncer:seatnotifications.view'],
], function () {

    Route::get('/', [
        'as'         => 'seatnotifications.index',
        'uses'       => 'SeatNotificationsController@index',
    ]);

    Route::get('/notifications', [
        'as'         => 'seatnotifications.get.available.notification',
        'uses'       => 'SeatNotificationsController@getNotifications',
    ]);

    Route::group([
        'middleware' => ['bouncer:seatnotifications.configuration'],
    ], function () {

        Route::get('/configuration', [
            'as'         => 'seatnotifications.configuration',
            'uses'       => 'SeatNotificationsController@config',
        ]);
    });

    Route::group([
        'namespace' => 'Discord',
        'prefix' => 'discord',
    ], function () {

        include __DIR__ . '/Routes/NotificationChannel/Discord.php';

        Route::group([
            'middleware' => ['bouncer:seatnotifications.configuration'],
            'prefix' => 'configuration',
        ], function () {

            include __DIR__ . '/Routes/Configuration/Discord.php';
        }
        );
    });

    Route::group([
        'namespace' => 'Slack',
        'prefix' => 'slack',
    ], function () {

        include __DIR__ . '/Routes/NotificationChannel/Slack.php';

        Route::group([
            'middleware' => ['bouncer:seatnotifications.configuration'],
            'prefix' => 'configuration',
        ], function () {

            include __DIR__ . '/Routes/Configuration/Slack.php';
        }
        );
    });

    Route::group([
        'namespace' => 'Notifications',
        'prefix' => 'notifications',
    ], function () {

        include __DIR__ . '/Routes/Notification/RefreshToken.php';
        include __DIR__ . '/Routes/Notification/KillMail.php';

    });

}
);
