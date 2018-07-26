<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 26.07.2018
 * Time: 17:53
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Actions\Notifications;


use Herpaderpaldent\Seat\SeatNotifications\Models\Seatnotification;

class AddSeatNotification
{
    public function execute(array $data)
    {
        $character_id = $data['character_id'];
        $corporation_id = $data['corporation_id'];
        $method = $data['method'];
        $notification = $data['notification'];
        $webhook = $data['webhook'];

        $seat_notification = new Seatnotification;

        $seat_notification->character_id = $character_id;
        $seat_notification->corporation_id = $corporation_id;
        $seat_notification->method = $method;
        $seat_notification->notification = $notification;
        $seat_notification->webhook = $webhook;
        $seat_notification->save();

        return true;
    }

}