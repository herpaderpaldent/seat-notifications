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
    public $tries = 3;

    protected $discord;

    protected $parameters;

    protected $channel_id;

    public function __construct(int $channel, array $parameters)
    {

        $this->parameters = $parameters;
        $this->channel_id = $channel;

        array_push($this->tags, 'channel_id:' . $this->channel_id);
    }

    public function handle()
    {

        $this->discord = app('seatnotifications-discord');

        Redis::funnel('channel_id_' . $this->channel_id)->limit(1)->then(function () {

            try {

                $this->discord->channel->createMessage($this->parameters);
            } catch (Exception $e) {

                if ($e->getResponse()->getStatusCode() === 429) {

                    $response = json_decode($e->getResponse()->getBody()->getContents());

                    //retry_after is in ms, so we need to convert it to seconds.
                    SendDiscordNotification::dispatch($this->channel_id, $this->parameters)
                        ->onQueue($this->queue)
                        ->delay(now()->addSeconds((int) $response->retry_after / 1000));

                    $this->delete();
                }

                $this->fail($e);
            }
        }, function () {

            // Could not obtain lock...
            logger()->debug('Could not dispatch SendDiscordNotification job for channel.id: ' . $this->channel_id);

            $this->release(10);
        });
    }
}
