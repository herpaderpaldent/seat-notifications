<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.12.2018
 * Time: 08:42.
 */
Route::group([
    'middleware' => ['bouncer:seatnotifications.kill_mail'],
], function () {

    Route::get('/kill_mail/{via}/subscribe/private', [
        'as'   => 'seatnotifications.kill_mail.subscribe.user',
        'uses' => 'KillMailController@subscribeDm',
    ]);

    Route::get('/kill_mail/{via}/unsubscribe/private', [
        'as'   => 'seatnotifications.kill_mail.unsubscribe.user',
        'uses' => 'KillMailController@unsubscribeDm',
    ]);
});

Route::group([
    'middleware' => ['bouncer:seatnotifications.kill_mail', 'bouncer:seatnotifications.configuration'],
], function () {

    Route::post('/kill_mail/channel', [
        'as'   => 'seatnotifications.kill_mail.subscribe.channel',
        'uses' => 'KillMailController@subscribeChannel',
    ]);

    Route::get('/kill_mail/{via}/unsubscribe', [
        'as'   => 'seatnotifications.kill_mail.unsubscribe.channel',
        'uses' => 'KillMailController@unsubscribeChannel',
    ]);

});
