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

    public function routeNotificationForDiscord()
    {
        return 525769280571310090;
    }

    public function deleting(RefreshToken $refresh_token)
    {
        Log::info('SoftDelete detected of '. $refresh_token->user->name);

        //TODO: Only send UNIQUE per webhook (i rather rename this to deliverychannel)
        //TODO: Figure out a way to limit only 1 channel per notification (setup per corp) but unlimited personal notifications.

        $arr = [
            'discord' => [
                'channel_id' => 441330906356121622
            ]
        ];

        Seatnotification::all()->each(function ($notification) use ($refresh_token, $arr){
            $this->notify(new RefreshTokenDeleted($refresh_token, $arr));
        });

    }
    public function test()
    {
        //$this->notify(new RefreshTokenDeleted(RefreshToken::find(95725047),"www.discord.link",'discord'));

        $arr = [
            'discord' => [
                'channel_id' => 525769280571310090
            ]
        ];

        $refresh_token = RefreshToken::find(95725047);
        return $this->notify(new RefreshTokenDeleted($refresh_token, $arr));
    }

}