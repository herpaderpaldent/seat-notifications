<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 07.07.2018
 * Time: 16:42.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Observers;

use Herpaderpaldent\Seat\SeatNotifications\Models\RefreshTokenNotification;
use Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshTokenDeletedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Seat\Eveapi\Models\RefreshToken;

class RefreshTokenObserver
{
    public function deleting(RefreshToken $refresh_token)
    {
        Redis::funnel('soft_delete:refresh_token_' . $refresh_token->user->name)->limit(1)->then(function () use ($refresh_token) {
            logger()->info('SoftDelete detected of ' . $refresh_token->user->name);

            $receipients = RefreshTokenNotification::all()
                ->filter(function ($recepient) {
                    return $recepient->shouldReceive();
                });

            Notification::send($receipients, (new RefreshTokenDeletedNotification($refresh_token)));
        }, function () use ($refresh_token) {
            logger()->info('A Soft-Delete job is already running for ' . $refresh_token->user->name);
        });
    }

    public function test()
    {

        $refresh_token = RefreshToken::find(95725047);

        Redis::funnel('soft_delete:refresh_token_' . $refresh_token->user->name)->limit(1)->then(function () use ($refresh_token) {
            logger()->info('SoftDelete detected of ' . $refresh_token->user->name);

            $receipients = RefreshTokenNotification::all()
                ->filter(function ($recepient) {
                    return $recepient->shouldReceive();
                });

            Notification::send($receipients, (new RefreshTokenDeletedNotification($refresh_token)));
        }, function () use ($refresh_token) {
            logger()->info('A Soft-Delete job is already running for ' . $refresh_token->user->name);
        });

    }
}
