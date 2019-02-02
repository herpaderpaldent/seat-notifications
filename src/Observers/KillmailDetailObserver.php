<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 23.01.2019
 * Time: 18:34.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Observers;

use Herpaderpaldent\Seat\SeatNotifications\Jobs\KillmaillDispatcher;
use function Psy\debug;
use Seat\Eveapi\Models\Killmails\KillmailDetail;

class KillmailDetailObserver
{
    /**
     * Listen to the CorproationKillmail created event.
     *
     * @param \Seat\Eveapi\Models\Killmails\CorporationKillmail $corporation_killmail
     *
     * @return void
     */
    public function created(KillmailDetail $killmail_detail)
    {

        logger()->debug('Observer is dispatching an Killmail Notification for killmail_id: ' . $killmail_detail->killmail_id);

        KillmaillDispatcher::dispatch($killmail_detail)
            ->delay(now()->addMinutes(1))
            ->onQueue('high');
    }

    public function test()
    {
        $killmail_detail = KillmailDetail::first();

        $job = new KillmaillDispatcher($killmail_detail);

        dispatch($job)->onQueue('high');
    }
}
