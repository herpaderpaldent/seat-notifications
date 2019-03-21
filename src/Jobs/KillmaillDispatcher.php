<?php
/**
 * MIT License.
 *
 * Copyright (c) 2019. Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Jobs;

use Herpaderpaldent\Seat\SeatNotifications\Models\NotificationRecipient;
use Herpaderpaldent\Seat\SeatNotifications\Notifications\KillMail\AbstractKillMailNotification;
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

        array_push($this->tags, 'killmail_id: ' . $this->killmail_id);
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

            $recipients = NotificationRecipient::all()
                ->filter(function ($recepient) {
                    return $recepient->shouldReceive(AbstractKillMailNotification::class, $this->getFilteredCorporationIds());
                });

            logger()->debug('and for corporations: ' . implode(', ', $this->getFilteredCorporationIds()));

            if($recipients->isEmpty()){
                logger()->debug('No Receiver found for this Notification. This job is going to be deleted.');
                $this->delete();
            }

            $recipients->groupBy('driver')
                ->each(function ($grouped_recipients) {
                    $driver = (string) $grouped_recipients->first()->driver;
                    $notification_class = AbstractKillMailNotification::getDriverImplementation($driver);

                    Notification::send($grouped_recipients, (new $notification_class($this->killmail_id)));
                });

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

        //TODO: check if delay if victim corporation_id is not available.
        $victim_corporation_id = optional($this->killmail_detail->victims)->corporation_id;

        return $attacker_corporation_ids
            ->push($victim_corporation_id)
            ->filter()
            ->toArray();
    }
}
