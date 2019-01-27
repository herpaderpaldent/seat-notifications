<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 26.12.2018
 * Time: 15:02.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications;

use Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\BaseNotificationController;
use Herpaderpaldent\Seat\SeatNotifications\Http\Validation\AddRefreshTokenChannelSubscriptionRequest;
use Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordUser;
use Herpaderpaldent\Seat\SeatNotifications\Models\RefreshTokenNotification;
use Herpaderpaldent\Seat\SeatNotifications\Models\Slack\SlackUser;
use Seat\Web\Models\Group;

class RefreshTokenController extends BaseNotificationController
{
    public function getNotification() : string
    {
        return 'seatnotifications::refresh_token.notification';
    }

    public function getPrivateView() : string
    {
        return 'seatnotifications::refresh_token.private';
    }

    public function getChannelView() : string
    {

        return 'seatnotifications::refresh_token.channel';
    }

    public function subscribeDm($via)
    {
        $channel_id = $this->getPrivateChannel($via);

        if(is_null($channel_id))
            return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');

        if($this->subscribeToChannel($channel_id, $via, 'refresh_token'))
            return redirect()->back()->with('success', 'You are going to be notified if someone deletes his refresh_token.');

        return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');
    }

    public function unsubscribeDm($via)
    {
        $channel_id = $this->getPrivateChannel($via);

        if(is_null($channel_id))
            return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');

        if($this->unsubscribeFromChannel($channel_id, 'refresh_token'))
            return redirect()->back()->with('success', 'You are no longer going to be notified about deleted refresh_tokens.');

        return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');
    }

    public function subscribeChannel()
    {

        $via = (string) request('via');
        $channel_id = (string) request('channel_id');

        if(is_null($channel_id) || is_null($channel_id))
            return abort(500);

        if($this->subscribeToChannel($channel_id, $via, 'refresh_token', true))
            return redirect()->back()->with('success', 'Channel will receive refresh_token notifications from now on.');

        return abort(500);
    }

    public function unsubscribeChannel($channel)
    {
        $channel_id = $this->getChannelChannelId($channel,'refresh_token');

        if(is_null($channel_id))
            return abort(500);

        if($this->unsubscribeFromChannel($channel_id, 'refresh_token'))
            return redirect()->back()->with('success', 'Channel will not receive refresh_token notifications from this point on.');

        return abort(500);
    }

    public function isSubscribed($view, $channel)
    {
        return $this->getSubscribtionStatus($channel, $view, 'refresh_token');
    }

    public function isDisabledButton($view, $channel) : bool
    {
        return $this->isDisabled($channel, $view, 'refresh_token');
    }

    public function isAvailable() : bool
    {
        return $this->hasPermission('refresh_token');
    }
}
