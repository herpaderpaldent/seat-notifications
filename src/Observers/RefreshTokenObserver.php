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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Seat\Eveapi\Models\RefreshToken;

class RefreshTokenObserver
{
    public function deleting(RefreshToken $refresh_token)
    {
        Log::info('SoftDelete detected of ' . $refresh_token->user->name);

        $receipients = RefreshTokenNotification::all()
            ->filter(function ($recepient) {
                return $recepient->shouldReceive();
            });

        Notification::send($receipients, (new RefreshTokenDeletedNotification($refresh_token)));

    }

    public function test()
    {
        $receipients = RefreshTokenNotification::all()
            ->filter(function ($recepient) {
                return $recepient->shouldReceive();
            });

        $refresh_token = RefreshToken::find(95725047);

        Notification::send($receipients, (new RefreshTokenDeletedNotification($refresh_token)));

    }
}
