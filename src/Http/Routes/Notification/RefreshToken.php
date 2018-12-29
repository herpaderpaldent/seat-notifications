<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.12.2018
 * Time: 08:42.
 */
Route::group([
    'middleware' => ['bouncer:seatnotifications.refresh_token'],
], function () {

    Route::get('/refresh_token/{via}/subscribe/private', [
        'as'   => 'seatnotifications.refresh_token.subscribe.user',
        'uses' => 'RefreshTokenController@subscribeDm',
    ]);

    Route::get('/refresh_token/{via}/unsubscribe/private', [
        'as'   => 'seatnotifications.refresh_token.unsubscribe.user',
        'uses' => 'RefreshTokenController@unsubscribeDm',
    ]);
});

Route::group([
    'middleware' => ['bouncer:seatnotifications.configuration'],
], function () {

    Route::post('/refresh_token/channel', [
        'as'   => 'seatnotifications.refresh_token.subscribe.channel',
        'uses' => 'RefreshTokenController@subscribeChannel',
    ]);

    Route::get('/refresh_token/{via}/unsubscribe', [
        'as'   => 'seatnotifications.refresh_token.unsubscribe.channel',
        'uses' => 'RefreshTokenController@unsubscribeChannel',
    ]);

});
