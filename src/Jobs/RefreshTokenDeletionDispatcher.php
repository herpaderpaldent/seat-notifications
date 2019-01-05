<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 05.01.2019
 * Time: 22:53.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Jobs;

use Herpaderpaldent\Seat\SeatNotifications\Models\RefreshTokenNotification;
use Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshTokenDeletedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Seat\Eveapi\Models\RefreshToken;

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

    public function __construct(RefreshToken $refresh_token)
    {
        $this->refresh_token = $refresh_token;
    }

    public function handle()
    {
        Redis::funnel('soft_delete:refresh_token_' . $this->refresh_token->user->name)->limit(1)->then(function () {
            logger()->info('SoftDelete detected of ' . $this->refresh_token->user->name);

            $recipients = RefreshTokenNotification::all()
                ->filter(function ($recepient) {
                    return $recepient->shouldReceive();
                });

            Notification::send($recipients, (new RefreshTokenDeletedNotification($this->refresh_token)));
        }, function () {

            logger()->info('A Soft-Delete job is already running for ' . $this->refresh_token->user->name);
            $this->delete();
        });
    }
}
