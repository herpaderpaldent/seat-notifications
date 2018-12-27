<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 23.12.2018
 * Time: 11:13
 */

//These routes are meant for configuration purposes and require seatnotification.configuration permission

Route::post('/', [
    'as'   => 'herpaderp.seatnotifications.discord.post.configuration',
    'uses' => 'DiscordServerController@postConfiguration',
]);

Route::get('/callback/discord/server', [
    'as'   => 'seatnotifications.callback.discord.server',
    'uses' => 'DiscordServerController@callback',
]);

Route::get('/get/discord/channels', [
    'as'   => 'seatnotifications.get.discord.channels',
    'uses' => 'DiscordServerController@getChannels',
]);