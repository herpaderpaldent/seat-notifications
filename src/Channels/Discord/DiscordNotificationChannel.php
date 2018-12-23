<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 22.12.2018
 * Time: 21:55
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Channels\Discord;


use Herpaderpaldent\Seat\SeatNotifications\Channels\BaseNotificationChannel;

class DiscordNotificationChannel extends BaseNotificationChannel
{
    public function getSettingView()
    {
        return view('seatnotifications::discord.settings')->render();
    }

}