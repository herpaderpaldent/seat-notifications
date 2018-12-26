<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 22.12.2018
 * Time: 21:55
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Models\Discord;


use Herpaderpaldent\Seat\SeatNotifications\Models\BaseNotificationChannel;

class DiscordNotificationChannel extends BaseNotificationChannel
{
    //TODO: check if can be moved to controller
    public function getSettingsView()
    {
        return view('seatnotifications::discord.settings')->render();
    }

    public function getRegistrationView()
    {
        return view('seatnotifications::discord.registration')->render();
    }


}