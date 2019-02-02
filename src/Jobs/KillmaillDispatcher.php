<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 05.01.2019
 * Time: 22:53.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Jobs;

use Herpaderpaldent\Seat\SeatNotifications\Models\SeatNotificationRecipient;
use Herpaderpaldent\Seat\SeatNotifications\Notifications\KillMailNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Seat\Eveapi\Models\Killmails\KillmailDetail;

class KillmaillDispatcher extends SeatNotificationsJobBase
{
    /**
     * @var array
     */
    protected $tags = ['kill_mail'];

    /**
     * @var
     */
    private $killmail_id;

    /**
     * @var array
     */
    private $filtered_corporation_ids;

    /**
     * RefreshTokenDeletionDispatcher constructor.
     *
     * @param \Seat\Eveapi\Models\Killmails\CorporationKillmail $killmail_id
     */
    public function __construct(KillmailDetail $killmail_detail)
    {
        logger()->debug('Construct KillmailDispatcher: ' . $killmail_detail->killmail_id);

        $this->killmail_id = $killmail_detail->killmail_id;

        $this->filtered_corporation_ids = $killmail_detail
            ->attackers
            ->map(function ($attacker) {
                return $attacker->corporation_id;
            })
            ->push(optional($killmail_detail->victims)->corporation_id)
            ->toArray();

    }

    public function handle()
    {
        Redis::funnel('killmail_id:' . $this->killmail_id)->limit(1)->then(function () {
            logger()->debug('Killmail notification for ID: ' . $this->killmail_id);

            $recipients = SeatNotificationRecipient::all()
                ->filter(function ($recepient) {
                    return $recepient->shouldReceive('kill_mail', $this->filtered_corporation_ids);
                });

            Notification::send($recipients, (new KillMailNotification($this->killmail_id)));
        }, function () {

            logger()->debug('A Killmail job is already running for ' . $this->killmail_id);
            $this->delete();
        });
    }
}
