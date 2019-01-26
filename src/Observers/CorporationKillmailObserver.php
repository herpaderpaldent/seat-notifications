<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 23.01.2019
 * Time: 18:34
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Observers;

use Herpaderpaldent\Seat\SeatNotifications\Models\SeatNotificationRecipient;
use Herpaderpaldent\Seat\SeatNotifications\Notifications\KillMailNotification;
use Illuminate\Support\Facades\Notification;
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
        logger()->debug('created triggerd');

        $recipients = SeatNotificationRecipient::where('notification','kill_mail')
            ->filter(function ($recepient) {
                return $recepient->shouldReceive();
            });

        Notification::send($recipients, (new KillMailNotification($corporation_killmail)));
    }

    public function test()
    {
        $recipients = SeatNotificationRecipient::all()//->where('notification','kill_mail')
            ->filter(function ($recepient) {
                return $recepient->shouldReceive();
            });

        $corporation_killmail = CorporationKillmail::first();

        Notification::send($recipients, (new KillMailNotification($corporation_killmail)));

    }


}