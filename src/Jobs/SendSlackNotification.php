<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 12.01.2019
 * Time: 14:24
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
                if($e->getCode() === 429)
                    return $this->release(10);

                report($e);
                return $this->delete();
            }
        }, function () {
            // Could not obtain lock...

            return $this->release(10);
        });
    }

}