<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.12.2018
 * Time: 21:10.
 */
Route::get('/', [
    'as'   => 'seatnotifications.register.slack',
    'uses' => 'SlackUserOAuthController@join',
]);

Route::get('/callback/user', [
    'as'   => 'seatnotifications.callback.slack.user',
    'uses' => 'SlackUserOAuthController@callback',
]);
