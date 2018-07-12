<?php
/**
 * Created by PhpStorm.
 * User: Mutterschiff
 * Date: 11.02.2018
 * Time: 16:57
 */

Route::group([
    'namespace' => 'Herpaderpaldent\Seat\SeatNotifications\Http\Controllers',
    'prefix' => 'seatnotifications'
    ], function() {

        //
        Route::group([
            'middleware' => ['web', 'auth']
        ], function (){
            Route::get('/', [
                'as'   => 'seatnotifications.index',
                'uses' => 'SeatNotificationsController@index'
                ]);
            Route::post('/webhook', [
                'as'   => 'seatnotifications.post.webhook',
                'uses' => 'SeatNotificationsController@postWebhook'
            ]);
            }
        );
    }
);


