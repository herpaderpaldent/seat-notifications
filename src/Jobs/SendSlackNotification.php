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

class SendSlackNotification extends SeatNotificationsJobBase
{
    /**
     * @var array
     */
    protected $tags = ['slack_notification'];

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 100;

    protected $parameters;

    protected $slack;

    public function __construct(array $parameters)
    {

        $this->parameters = $parameters;
    }

    public function handle()
    {
        $this->slack = app('slack');

        Redis::throttle('slack_notification')->allow(1)->every(60)->then(function () {
            try {

                $this->slack->chatPostMessage($this->parameters);
            } catch (Exception $e) {

                if($e->getCode() === 429) {
                    SendSlackNotification::dispatch($this->parameters)
                        ->onQueue($this->queue)
                        ->delay(now()->addMinute());

                    $this->delete();
                }

                $this->fail($e);
            }
        }, function () {
            // Could not obtain lock...

            logger()->debug('SendSlackNotification job is already running. Delay job by 10 second.');
            $this->release(10);
        });
    }
}
