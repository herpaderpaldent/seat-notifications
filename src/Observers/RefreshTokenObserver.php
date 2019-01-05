<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 07.07.2018
 * Time: 16:42.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Observers;

use Herpaderpaldent\Seat\SeatNotifications\Jobs\RefreshTokenDeletionDispatcher;
use Seat\Eveapi\Models\RefreshToken;

class RefreshTokenObserver
{
    public function deleting(RefreshToken $refresh_token)
    {

        $job = new RefreshTokenDeletionDispatcher($refresh_token);

        dispatch($job)->onQueue('high');
    }

    public function test()
    {

        $refresh_token = RefreshToken::find(95725047);

        $job = new RefreshTokenDeletionDispatcher($refresh_token);

        dispatch($job)->onQueue('high');

    }
}
