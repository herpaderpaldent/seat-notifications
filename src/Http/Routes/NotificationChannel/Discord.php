<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 23.12.2018
 * Time: 23:19.
 */

//These routes are meant for the average user that has seatnotifications.view permission

Route::get('/', [
    'as'   => 'seatnotifications.register.discord',
    'uses' => 'DiscordOAuthController@join',
]);

Route::get('/callback/user', [
    'as'   => 'seatnotifications.callback.discord.user',
    'uses' => 'DiscordOAuthController@callback',
]);
