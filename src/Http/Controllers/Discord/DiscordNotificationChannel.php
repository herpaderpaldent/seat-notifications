<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 22.12.2018
 * Time: 21:55
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Discord;


use Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\BaseNotificationChannel;

class DiscordNotificationChannel extends BaseNotificationChannel
{
    public function getSettingsView()
    {
        return 'seatnotifications::discord.settings';
    }

    public function getRegistrationView()
    {
        return 'seatnotifications::discord.registration';
    }


}