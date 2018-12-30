<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 22.12.2018
 * Time: 21:55.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Discord;

use Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\BaseNotificationChannel;

class DiscordNotificationChannelController extends BaseNotificationChannel
{
    public function getSettingsView()
    {
        return 'seatnotifications::discord.settings';
    }

    public function getRegistrationView()
    {
        return 'seatnotifications::discord.registration';
    }

    public function getChannels()
    {
        if(is_null(setting('herpaderp.seatnotifications.discord.credentials.guild_id', true)))
            return redirect()->back()->with('error', 'No guild_id detected, have you setup your discord bot correctly?');

        $response = cache('herpaderp.seatnotifications.discord.channels');

        if(is_null($response)){
            $channels = app('discord')
                ->guild
                ->getGuildChannels([
                    'guild.id' => (int) setting('herpaderp.seatnotifications.discord.credentials.guild_id', true),
                ]);

            $response = [];
            foreach ($channels as $channel) {
                if($channel->type === 0)
                    $response[$channel->id] = $channel->name;
            }

            cache(['herpaderp.seatnotifications.discord.channels' => $response], now()->addMinutes(5));
        }

        return ['discord' => $response];

    }
}
