<?php
/**
 * Created by PhpStorm.
 * User: Mutterschiff
 * Date: 11.02.2018
 * Time: 16:57.
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
