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

use Herpaderpaldent\Seat\SeatNotifications\Models\Slack\SlackUser;

class SlackNotificationDriver extends AbstractNotificationDriver
{
    /**
     * The view name which will be used to store the channel settings.
     *
     * @return string
     */
    public static function getSettingsView(): string
    {
        return 'seatnotifications::slack.settings';
    }

    /**
     * @return string
     */
    public static function getButtonLabel() : string
    {
        return 'Slack';
    }

    /**
     * @return string
     */
    public static function getButtonIconClass() : string
    {
        return 'fa-slack';
    }

    /**
     * @return array
     */
    public static function getChannels(): array
    {
        return cache()->remember('herpaderp.seatnotifications.slack.channels', 5, function () {

            $response = collect();

            // retrieve a list of channels from the registered Slack
            $channels = app('slack')
                ->conversationsList([
                    'exclude_archived' => true,
                    'types' => 'public_channel,private_channel',
                ])
                ->getChannels();

            foreach ($channels as $channel)
                $response->push([
                    'id'              => $channel->id,
                    'name'            => $channel->name,
                    'private_channel' => $channel->is_group,
                ]);

            return $response->toArray();
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
        return ! is_null(setting('herpaderp.seatnotifications.slack.credentials.token', true));
    }

    /**
     * Return driver_id of user.
     *
     * @return string
     */
    public static function getPrivateChannel() : ?string
    {
        return optional(SlackUser::find(auth()->user()->group->id))->channel_id;
    }

    /**
     * Return the route key which have to be used in a private notification registration flow.
     *
     * @return string
     */
    public static function getPrivateRegistrationRoute(): ?string
    {
        return 'seatnotifications.register.slack';
    }
}
