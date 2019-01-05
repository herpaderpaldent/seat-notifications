<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 22.12.2018
 * Time: 21:55.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Discord;

use Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\BaseNotificationChannelController;

class DiscordNotificationChannelController extends BaseNotificationChannelController
{
    public function getSettingsView() : string
    {
        return 'seatnotifications::discord.settings';
    }

    public function getRegistrationView() : string
    {
        return 'seatnotifications::discord.registration';
    }

    public function getChannels()
    {
        if(is_null(setting('herpaderp.seatnotifications.discord.credentials.bot_token', true)))
            return ['discord' => []];

        $response = cache('herpaderp.seatnotifications.discord.channels');

        if(is_null($response)){
            $channels = app('seatnotifications-discord')
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
