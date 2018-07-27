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
            $this->notify(new RefreshTokenDeleted($refresh_token,$notification->webhook,$notification->method));
        });

    }
    public function test()
    {
        Log::info('Showing user profile for user: ');
        //$this->notify(new RefreshTokenDeleted(RefreshToken::find(95725047),"https://discordapp.com/api/webhooks/472157574247219201/jV-JaP5GdT7CH_ej5jHnNZWHlHvnT9iA3VG7T_XP7jcuG8kPIBDfbwBDXK6n-iuuH5MA",'discord'));
        $this->notify(new RefreshTokenDeleted(RefreshToken::find(95725047),"#test",'slack'));
    }

}