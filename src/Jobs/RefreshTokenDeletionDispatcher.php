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
use Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken\AbstractRefreshTokenNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Seat\Eveapi\Models\RefreshToken;

/**
 * Class RefreshTokenDeletionDispatcher.
 * @package Herpaderpaldent\Seat\SeatNotifications\Jobs
 */
class RefreshTokenDeletionDispatcher extends SeatNotificationsJobBase
{
    /**
     * @var array
     */
    protected $tags = ['refresh_token_deletion'];

    /**
     * @var \Seat\Eveapi\Models\RefreshToken
     */
    private $refresh_token;

    /**
     * RefreshTokenDeletionDispatcher constructor.
     *
     * @param \Seat\Eveapi\Models\RefreshToken $refresh_token
     */
    public function __construct(RefreshToken $refresh_token)
    {
        $this->refresh_token = $refresh_token;

        array_push($this->tags, 'user_id:' . $this->refresh_token->user->id);
    }

    public function handle()
    {
        Redis::funnel('soft_delete:refresh_token_' . $this->refresh_token->user->id)->limit(1)->then(function () {
            logger()->info('SoftDelete detected of ' . $this->refresh_token->user->name);

            $recipients = NotificationRecipient::all()
                ->filter(function ($recipient) {
                    return $recipient->shouldReceive(AbstractRefreshTokenNotification::class);
                });

            if($recipients->isEmpty()){
                logger()->debug('No Receiver found for this Notification. This job is going to be deleted.');
                $this->delete();
            }

            $recipients->groupBy('driver')
                ->each(function ($grouped_recipients) {
                    $driver = (string) $grouped_recipients->first()->driver;
                    $notification_class = AbstractRefreshTokenNotification::getDriverImplementation($driver);

                    Notification::send($grouped_recipients, (new $notification_class($this->refresh_token)));
                });

        }, function () {

            logger()->debug('A Soft-Delete job is already running for ' . $this->refresh_token->user->name);
            $this->delete();
        });
    }
}
