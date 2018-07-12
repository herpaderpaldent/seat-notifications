<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 11.07.2018
 * Time: 22:08
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Actions\Discord;


use Herpaderpaldent\Seat\SeatNotifications\Models\Seatnotification;

class AddWebhookAction
{
    public function execute(array $data)
    {
        $method = $data['method'];
        $notification = $data['notification'];
        $webhook = $data['webhook'];

        $seat_notification = new Seatnotification;

        $seat_notification->character_id = null;
        $seat_notification->corporation_id = null;
        $seat_notification->method = $method;
        $seat_notification->notification = $notification;
        $seat_notification->webhook = $webhook;
        $seat_notification->save();

        return true;

    }

}