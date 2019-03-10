<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 09.03.2019
 * Time: 11:32
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Actions;


use Exception;
use Herpaderpaldent\Seat\SeatNotifications\Models\NotificationRecipient;

class GetPublicDriverId
{
    protected $notification;

    protected $driver;

    public function execute(array $request) {

        $this->driver = $request['driver'];
        $this->notification = $request['notification'];

        $driver_id = null;

        try {
            $driver_id =  NotificationRecipient::where('driver', $this->driver)
                ->where('group_id', null)
                ->get()
                ->map(function ($recipient) {
                    return $recipient
                        ->subscriptions
                        ->filter(function ($subscription){
                            return $subscription->notification === $this->notification;
                        });
                })
                ->flatten()
                ->first()
                ->recipient
                ->driver_id;

        } catch (Exception $exception) {


        }

        return $driver_id;

    }

}