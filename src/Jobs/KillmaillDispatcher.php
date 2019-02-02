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
    protected $tags = ['kill_mail', 'dispatcher'];

    /**
     * @var
     */
    private $killmail_id;

    /**
     * @var
     */
    private $killmail_detail;

    /**
     * RefreshTokenDeletionDispatcher constructor.
     *
     * @param \Seat\Eveapi\Models\Killmails\KillmailDetail $killmail_detail
     */
    public function __construct(KillmailDetail $killmail_detail)
    {
        logger()->debug('Construct KillmailDispatcher: ' . $killmail_detail->killmail_id);

        $this->killmail_detail = $killmail_detail;
        $this->killmail_id = $killmail_detail->killmail_id;

        $this->tags = array_merge($this->tags, [
            'killmail_id: ' . $this->killmail_id,
        ]);
    }

    public function handle()
    {

        // Check if victim and attacker details are present yet.
        if(empty($this->killmail_detail->victims) || empty($this->killmail_detail->attackers)){

            logger()->debug('Either victim or attacker information are missing. Delaying the job for a minute');
            KillmaillDispatcher::dispatch($this->killmail_detail)->onQueue($this->queue)->delay(now()->addMinute());
            $this->delete();
        }

        Redis::funnel('killmail_id:' . $this->killmail_id)->limit(1)->then(function () {
            logger()->debug('Killmail notification for ID: ' . $this->killmail_id);

            $recipients = SeatNotificationRecipient::all()
                ->filter(function ($recepient) {
                    return $recepient->shouldReceive('kill_mail', $this->getFilteredCorporationIds());
                });

            logger()->debug('and for corporations: ' . implode(', ', $this->getFilteredCorporationIds()));

            Notification::send($recipients, (new KillMailNotification($this->killmail_id)));
        }, function () {

            logger()->debug('A Killmail job is already running for ' . $this->killmail_id);
            $this->delete();
        });
    }

    /**
     * @return array
     */
    private function getFilteredCorporationIds() : array
    {
        $attacker_corporation_ids = $this->killmail_detail
            ->attackers
            ->map(function ($attacker) {
                return $attacker->corporation_id;
            });

        $victim_corporation_id = optional($this->killmail_detail->victims)->corporation_id;

        return $attacker_corporation_ids
            ->push($victim_corporation_id)
            ->filter()
            ->toArray();
    }
}
