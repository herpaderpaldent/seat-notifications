<?php
/**
 * Created by PhpStorm.
 * User: Mutterschiff
 * Date: 11.02.2018
 * Time: 16:57
 */

Route::group([
    'namespace' => 'Herpaderpaldent\Seat\SeatNotifications\Http\Controllers',
    'prefix'    => 'seatnotifications',
    'middleware' => ['web', 'auth', 'bouncer:seatnotifications.view'],
], function () {

    Route::get('/configuration', [
        'as'         => 'seatnotifications.configuration',
        'uses'       => 'SeatNotificationsController@config',
    ]);

    Route::group([
        'namespace' => 'Discord',
    ], function () {

        include __DIR__. '/Routes/NotificationChannel/Discord.php';

        Route::group([
            'middleware' => ['bouncer:seatnotifications.configuration'],
            'prefix' => 'configuration',
        ], function () {

            include __DIR__ . '/Routes/Configuration/Discord.php';
        }
        );
    });

}
);


