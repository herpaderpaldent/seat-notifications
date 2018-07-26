<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 26.07.2018
 * Time: 12:07
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Channels\SlackWebhook;

use Illuminate\Notifications\Notification;
use Seat\Services\Settings\Seat;

class SlackWebhookChannel extends \Illuminate\Notifications\Channels\SlackWebhookChannel
{
    public function send($notifiable, Notification $notification)
    {
        if(empty(Seat::get('slack_webhook'))){
            return;
        }

        $url = Seat::get('slack_webhook');
        $this->http->post($url, $this->buildJsonPayload(
            $notification->toSlack($notifiable)
        ));
    }

}