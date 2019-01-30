<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 23.01.2019
 * Time: 18:34
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Observers;

use Herpaderpaldent\Seat\SeatNotifications\Jobs\CorporationKillmaillDispatcher;
use Herpaderpaldent\Seat\SeatNotifications\Models\SeatNotificationRecipient;
use Herpaderpaldent\Seat\SeatNotifications\Notifications\KillMailNotification;
use Illuminate\Support\Facades\Notification;
use function Psy\debug;
use Seat\Eveapi\Models\Killmails\CorporationKillmail;

class CorporationKillmailObserver
{
    /**
     * Listen to the CorproationKillmail created event.
     *
     * @param \Seat\Eveapi\Models\Killmails\CorporationKillmail $corporation_killmail
     *
     * @return void
     */
    public function created(CorporationKillmail $corporation_killmail)
    {

        $job = new CorporationKillmaillDispatcher($corporation_killmail);

        dispatch($job)->onQueue('high');
    }

    public function test()
    {
        $corporation_killmail = CorporationKillmail::where('corporation_id', 98534270)->first();

        $job = new CorporationKillmaillDispatcher($corporation_killmail);

        dispatch($job)->onQueue('high');

    }


}