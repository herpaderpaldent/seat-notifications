<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 26.07.2018
 * Time: 14:52
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Actions\SeAT;


use Seat\Services\Settings\Seat;

class AddSlackWebhook
{
    public function execute(array $data)
    {
        $webhook = $data['webhook'];

        Seat::set('slack_webhook',$webhook);

        return true;
    }

}