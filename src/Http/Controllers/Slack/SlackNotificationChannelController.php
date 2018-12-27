<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.12.2018
 * Time: 20:55
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Slack;


use Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\BaseNotificationChannel;

class SlackNotificationChannelController extends BaseNotificationChannel
{
    public function getSettingsView()
    {
        return 'seatnotifications::slack.settings';
    }

    public function getRegistrationView()
    {
        return 'seatnotifications::slack.registration';
    }

}