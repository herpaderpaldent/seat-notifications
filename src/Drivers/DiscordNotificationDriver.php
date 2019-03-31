<?php
/**
 * MIT License.
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
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Drivers;

use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordChannel;
use Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordUser;

/**
 * Class DiscordChannel.
 * @package Herpaderpaldent\Seat\SeatNotifications\Http\Channels\Discord
 */
class DiscordNotificationDriver extends AbstractNotificationDriver
{
    /**
     * The view name which will be used to store the channel settings.
     *
     * @return string
     */
    public static function getSettingsView(): string
    {

        return 'seatnotifications::discord.settings';
    }

    /**
     * @return string
     */
    public static function getButtonLabel(): string
    {

        return 'Discord';
    }

    /**
     * @return string
     */
    public static function getButtonIconClass(): string
    {

        return 'fa-bullhorn';
    }

    public static function getPrivateRegistrationRoute() : string
    {
        return 'seatnotifications.register.discord';
    }

    /**
     * @return array
     */
    public static function getChannels(): array
    {

        return cache()->remember('herpaderp.seatnotifications.discord.channels', 5, function () {

            $data = collect();

            // retrieve a list of channels from the registered Discord
            $channels = app('seatnotifications-discord')
                ->guild
                ->getGuildChannels([
                    'guild.id' => (int) setting('herpaderp.seatnotifications.discord.credentials.guild_id', true),
                ]);

            // building a simple key/name list which will be return as a valid channels list
            foreach ($channels as $channel) {
                if ($channel->type !== 0)
                    continue;

                $data->push([
                    'id'              => (string) $channel->id,
                    'name'            => $channel->name,
                    'private_channel' => false,
                ]);
            }

            return $data->toArray();
        });
    }

    /**
     * Determine if a channel is supporting private notifications.
     *
     * @return bool
     */
    public static function allowPersonalNotifications(): bool
    {

        return true;
    }

    /**
     * Determine if a channel is supporting public notifications.
     *
     * @return bool
     */
    public static function allowPublicNotifications(): bool
    {

        return true;
    }

    /**
     * Determine if a channel has been properly setup.
     *
     * @return bool
     */
    public static function isSetup(): bool
    {

        return ! is_null(setting('herpaderp.seatnotifications.discord.credentials.bot_token', true));
    }

    /**
     * Return driver_id of user.
     *
     * @return string
     */
    public static function getPrivateChannel(): ?string
    {

        return optional(DiscordUser::find(auth()->user()->group->id))->channel_id;
    }
}
