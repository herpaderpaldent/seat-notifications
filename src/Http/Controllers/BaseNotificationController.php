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

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers;

use Exception;
use Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordUser;
use Herpaderpaldent\Seat\SeatNotifications\Models\SeatNotification;
use Herpaderpaldent\Seat\SeatNotifications\Models\SeatNotificationRecipient;
use Herpaderpaldent\Seat\SeatNotifications\Models\Slack\SlackUser;
use Seat\Services\Repositories\Corporation\Corporation;
use Seat\Web\Http\Controllers\Controller;

abstract class BaseNotificationController extends Controller
{
    use Corporation;

    abstract public function getNotification() : string;

    abstract public function getPrivateView() : string;

    abstract public function getChannelView() : string;

    public function subscribeToChannel($channel_id, $via, $notification, $is_channel = false, $affiliation = null) : bool
    {

        try {
            SeatNotificationRecipient::firstOrCreate(
                ['channel_id' => $channel_id], ['notification_channel' => $via, 'is_channel' => $is_channel]
            )
                ->notifications()
                ->create(['name' => $notification, 'affiliation' => $affiliation]);

            return true;

        } catch (Exception $e) {

            return false;
        }
    }

    public function unsubscribeFromChannel($channel_id, $notification) : bool
    {
        try {
            SeatNotification::where('channel_id', $channel_id)
                ->where('name', $notification)
                ->delete();

            $empty_recipient = SeatNotificationRecipient::find($channel_id)
                ->notifications
                ->isEmpty();

            if($empty_recipient)
                SeatNotificationRecipient::find($channel_id)->delete();

            return true;

        } catch (Exception $e) {

            return false;
        }
    }

    public function getPrivateChannel($channel) : ?string
    {
        $channel_id = null;
        $group_id = auth()->user()->group->id;

        if ($channel === 'discord')
            $channel_id = DiscordUser::find($group_id)->channel_id;

        if ($channel === 'slack')
            $channel_id = SlackUser::find($group_id)->channel_id;

        return $channel_id;
    }

    public function getChannelChannelId(string $channel, string $notification) : ?string
    {
        return SeatNotification::where('name', $notification)
            ->get()
            ->filter(function ($notification) use ($channel) {
                return $notification->recipients->is_channel && $notification->recipients->notification_channel === $channel;
            })
            ->first()->channel_id;
    }

    public function getCorporations(string $view, string $channel, string $notification)
    {

        $subscribed_corporations = [];

        if($this->getSubscribtionStatus($channel, $view, $notification)) {
            $channel_id = null;

            if($view === 'private')
                $channel_id = $this->getPrivateChannel($channel);

            if($view === 'channel')
                $channel_id = $this->getChannelChannelId($channel, $notification);

            if(is_null($channel_id))
                abort(500);

            $subscribed_corporations = SeatNotification::where('channel_id', $channel_id)
                ->where('name', $notification)
                ->get()
                ->map(function ($seat_notification) {
                    return $seat_notification->affiliations()->corporations;
                })
                ->flatten()
                ->toArray();
        }

        return $this->getAllCorporationsWithAffiliationsAndFilters()
            ->map(function ($corporation) use ($subscribed_corporations) {
                return [
                    'corporation_id' => $corporation->corporation_id,
                    'name' => $corporation->name,
                    'subscribed' => in_array($corporation->corporation_id, $subscribed_corporations),
                    ];
            })->toArray();

    }

    /**
     * @param $channel
     * @param $view
     *
     * @return bool
     * @throws \Seat\Services\Exceptions\SettingException
     */
    public function isDisabled(string $channel, string $view, string $permission) : bool
    {
        if(! $this->hasPermission($permission, $view))
            return true;

        if(! $this->isChannelSetup($channel))
            return true;

        if($view === 'private')
            if(is_null($this->getPrivateChannel($channel)))
                return true;

        return false;
    }

    public function hasPermission(string $permission, string $view = '') : bool
    {
        if($view === 'channel')
            if(! auth()->user()->has('seatnotifications.configuration', false))
                return false;

        if(auth()->user()->has('seatnotifications.' . $permission, false))
            return true;

        return false;
    }

    public function isSlackSetup() : bool
    {
        if(is_null(setting('herpaderp.seatnotifications.slack.credentials.token', true)))
            return false;

        return true;
    }

    public function isDiscordSetup() : bool
    {
        if(is_null(setting('herpaderp.seatnotifications.discord.credentials.bot_token', true)))
            return false;

        return true;
    }

    public function isChannelSetup(string $channel) : bool
    {

        if($channel === 'slack')
            if($this->isSlackSetup())
                return true;

        if($channel === 'discord')
            if($this->isDiscordSetup())
                return true;

        return false;
    }

    public function getSubscribtionStatus(string $channel, string $view, string $notification) : bool
    {
        if($view === 'private')
            return $this->getPrivateSubscriptionStatus($channel, $notification);

        if($view === 'channel')
            return $this->getChannelSubscriptionStatus($channel, $notification);

        return false;

    }

    public function getPrivateSubscriptionStatus(string $channel, string $notification) : bool
    {

        $channel_id = $this->getPrivateChannel($channel);

        return SeatNotification::where('channel_id', $channel_id)
            ->where('name', $notification)
            ->get()
            ->isNotEmpty();
    }

    public function getChannelSubscriptionStatus(string $channel, string $notification) : bool
    {
        // TODO: check if could be simplified by getting channel_id similiar to getAvailableCorporation
        return SeatNotification::where('name', $notification)
            ->get()
            ->filter(function ($notification) use ($channel) {
                return $notification->recipients->is_channel && $notification->recipients->notification_channel === $channel;
            })
            ->isNotEmpty();
    }

    /*public function getChannelSubscribedCorporations(string $channel, string $notification)
    {
        return SeatNotification::where('name', $notification)
            ->get()
            ->filter(function ($notification) use ($channel) {
                return $notification->recipients->is_channel && $notification->recipients->notification_channel === $channel;
            })
            ->map(function ($notification) {
                return $notification->affiliations()->corporations;
            })
            ->flatten();
    }*/
}
