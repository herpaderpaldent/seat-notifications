<?php
/**
 * MIT License
 *
 * Copyright (c) 2019. Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
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
