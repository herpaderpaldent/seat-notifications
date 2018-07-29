<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 07.07.2018
 * Time: 16:42
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Observers;
use Herpaderpaldent\Seat\SeatNotifications\Models\Seatnotification;
use Herpaderpaldent\Seat\SeatNotifications\RefreshTokenDeleted;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Seat\Eveapi\Models\RefreshToken;

class RefreshTokenObserver
{
    use Notifiable;

    public function deleting(RefreshToken $refresh_token)
    {
        Log::info('SoftDelete detected of '. $refresh_token->user->name);

        //TODO: Only send UNIQUE per webhook (i rather rename this to deliverychannel)
        //TODO: Figure out a way to limit only 1 channel per notification (setup per corp) but unlimited personal notifications.

        Seatnotification::all()->each(function ($notification) use ($refresh_token){
            $this->notify(new RefreshTokenDeleted($refresh_token,$notification->webhook,$notification->method));
        });

    }
    public function test()
    {
        //$this->notify(new RefreshTokenDeleted(RefreshToken::find(95725047),"www.discord.link",'discord'));
        $this->notify(new RefreshTokenDeleted(RefreshToken::find(95725047),"#test",'slack'));
    }

}