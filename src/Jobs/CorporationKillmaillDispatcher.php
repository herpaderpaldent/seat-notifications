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
use Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshTokenDeletedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Seat\Eveapi\Models\Killmails\CorporationKillmail;
use Seat\Eveapi\Models\RefreshToken;

/**
 * Class RefreshTokenDeletionDispatcher
 * @package Herpaderpaldent\Seat\SeatNotifications\Jobs
 */
class CorporationKillmaillDispatcher extends SeatNotificationsJobBase
{
    /**
     * @var array
     */
    protected $tags = ['refresh_token_deletion'];

    /**
     * @var 
     */
    private $corporation_killmail_killmail_id;

    /**
     * @var array
     */
    private $filtered_corporation_ids;

    /**
     * RefreshTokenDeletionDispatcher constructor.
     *
     * @param \Seat\Eveapi\Models\Killmails\CorporationKillmail $corporation_killmail
     */
    public function __construct(CorporationKillmail $corporation_killmail)
    {
        $this->corporation_killmail_killmail_id = $corporation_killmail->killmail_id;

        $this->filtered_corporation_ids = $corporation_killmail
            ->killmail_detail
            ->attackers
            ->map(function ($attacker) {return $attacker->corporation_id;})
            ->push($corporation_killmail->killmail_victim->corporation_id)
            ->toArray();

    }

    public function handle()
    {
        Redis::funnel('killmail_' . $this->corporation_killmail_killmail_id)->limit(1)->then(function () {
            logger()->info('Killmail notification for ID: ' . $this->corporation_killmail_killmail_id);

            $recipients = SeatNotificationRecipient::all()
                ->filter(function ($recepient) {
                    return $recepient->shouldReceive('kill_mail', $this->filtered_corporation_ids);
                });

            Notification::send($recipients, (new KillMailNotification($this->corporation_killmail_killmail_id)));
        }, function () {

            logger()->info('A Killmail job is already running for ' . $this->corporation_killmail_killmail_id);
            $this->delete();
        });
    }
}
