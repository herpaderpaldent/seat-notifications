<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 23.12.2018
 * Time: 11:13
 */

Route::get('/', [
    'as'   => 'seatnotifications.configuration',
    'uses' => 'SeatNotificationsController@config',
]);

Route::get('/add', [
    'as'   => 'seatnotifications.post.configuration',
    'uses' => 'SeatNotificationsController@postConfiguration',
]);