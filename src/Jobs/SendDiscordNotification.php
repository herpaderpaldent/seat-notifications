<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 11.01.2019
 * Time: 22:11
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Jobs;


use Exception;
use Illuminate\Support\Facades\Redis;

class SendDiscordNotification extends SeatNotificationsJobBase
{
    /**
     * @var array
     */
    protected $tags = ['discord_notification'];

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 100;

    protected $discord;

    protected $channel;

    protected $payload;

    public function __construct(int $channel, $payload)
    {
        $this->channel = $channel;
        $this->payload = $payload;

    }

    public function handle()
    {

        $this->discord = app('seatnotifications-discord');

        Redis::funnel('discord_notification')->limit(1)->then(function () {

            try {
                $this->discord->channel->createMessage([
                    'channel.id' => (int) $this->channel,
                    'content'    => $this->payload['content'],
                    'embed'      => $this->payload['embeds'][0],
                ]);
            } catch (Exception $e) {
                $response = json_decode($e->getResponse()->getBody()->getContents());

                //retry_after is in ms, so we need to convert it to seconds.
                return $this->release((int) $response->retry_after / 1000);
            }

        }, function () {

            // Could not obtain lock...
            logger()->debug('Could not obtain lock...');

            return $this->release(10);
        });
    }

}