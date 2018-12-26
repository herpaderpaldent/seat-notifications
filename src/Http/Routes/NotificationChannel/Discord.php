<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 23.12.2018
 * Time: 23:19
 */

//These routes are meant for the average user that has seatnotifications.view permission

Route::get('/discord', [
    'as'   => 'seatnotifications.redirect.to.provider.discord',
    'uses' => 'SeatNotificationsDiscordController@redirectToProvider',
]);

/*Route::get('/callback/discord/', [
    'as'   => 'seatnotifications.callback.discord',
    'uses' => 'DiscordServerController@handleProviderCallback',
]);*/