<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 23.12.2018
 * Time: 11:13
 */

//These routes are meant for configuration purposes and require seatnotification.configuration permission

Route::post('/', [
    'as'   => 'herpaderp.seatnotifications.slack.post.configuration',
    'uses' => 'SlackServerOAuthController@postConfiguration',
]);

Route::get('/callback/server', [
    'as'   => 'seatnotifications.callback.slack.server',
    'uses' => 'SlackServerOAuthController@callback',
]);

Route::get('/channels', [
    'as'   => 'seatnotifications.get.slack.channels',
    'uses' => 'SlackNotificationChannelController@getChannels',
]);