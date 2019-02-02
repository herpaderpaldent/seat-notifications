<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 11.01.2019
 * Time: 22:11.
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

    protected $parameters;

    protected $payload;

    protected $channel_id;

    public function __construct(int $channel, array $parameters)
    {
        $this->parameters = $parameters;
        $this->channel_id = $channel;
    }

    public function handle()
    {

        $this->discord = app('seatnotifications-discord');

        Redis::funnel('channel_id: ' . $this->channel_id)->limit(1)->then(function () {

            try {

                $this->discord->channel->createMessage($this->parameters);
            } catch (Exception $e) {
                $response = json_decode($e->getResponse()->getBody()->getContents());

                //retry_after is in ms, so we need to convert it to seconds.
                SendDiscordNotification::dispatch($this->channel_id, $this->parameters)
                    ->onQueue($this->queue)
                    ->delay(now()->addSeconds((int) $response->retry_after / 1000));

                $this->delete();
            }

        }, function () {

            // Could not obtain lock...
            logger()->debug('Could not obtain lock...');

            return $this->release(10);
        });
    }
}
