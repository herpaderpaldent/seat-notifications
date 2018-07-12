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

        Seatnotification::all()->each(function ($notification) use ($refresh_token){
            $this->notify(new RefreshTokenDeleted($refresh_token,$notification->webhook));
        });

    }
    public function test()
    {
        Log::info('Showing user profile for user: ');
        $this->notify(new RefreshTokenDeleted(RefreshToken::find(95725047),"https://discordapp.com/api/webhooks/465115064408604672/u27v58-i6jrg5Siq9BwM2dK7Ir7RRUp9kxpHd3k_F90IrF8hx5H6eaWFbtTE3ejhnrFP"));
    }

}