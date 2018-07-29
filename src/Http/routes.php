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
                'uses' => 'SeatNotificationsController@index',
                'middleware' => 'bouncer:superuser'
                ]);
            Route::post('/seatnotification', [
                'as'   => 'seatnotifications.post.seat.notification',
                'uses' => 'SeatNotificationsController@postSeatNotification',
                'middleware' => 'bouncer:superuser'
            ]);
            Route::get('/seatnotification/{method}/{notification}/delete', [
                'as'   => 'seatnotifications.delete.seat.notification',
                'uses' => 'SeatNotificationsController@deleteSeatNotification',
                'middleware' => 'bouncer:superuser'
            ]);
            Route::post('/webhook/slack', [
                'as'   => 'seatnotifications.post.slack.webhook',
                'uses' => 'SeatNotificationsController@postSlackWebhook',
                'middleware' => 'bouncer:superuser'
            ]);
            Route::get('/webhook/slack/delete', [
                'as'   => 'seatnotifications.remove.slack.webhook',
                'uses' => 'SeatNotificationsController@removeSlackWebhook',
                'middleware' => 'bouncer:superuser'
            ]);
            }
        );
    }
);


