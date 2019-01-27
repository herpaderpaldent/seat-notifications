<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 26.12.2018
 * Time: 15:02.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications;

use Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\BaseNotificationController;

/**
 * Class KillMailController
 * @package Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications
 */
class KillMailController extends BaseNotificationController
{

    public function getNotification() : string
    {
        return 'seatnotifications::kill_mail.notification';
    }

    public function getPrivateView() : string
    {
        return 'seatnotifications::kill_mail.private';
    }

    public function getChannelView() : string
    {

        return 'seatnotifications::kill_mail.channel';
    }

    public function subscribeDm($via)
    {
        $channel_id = $this->getPrivateChannel($via);

        if(is_null($channel_id))
            return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');

        if($this->subscribeToChannel($channel_id, $via, 'kill_mail'))
            return redirect()->back()->with('success', 'You are going to be notified about killmails.');

        return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');
    }

    public function unsubscribeDm($via)
    {
        $channel_id = $this->getPrivateChannel($via);

        if(is_null($channel_id))
            return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');

        if($this->unsubscribeFromChannel($channel_id, 'kill_mail'))
            return redirect()->back()->with('success', 'You are no longer going to be notified about killmails.');

        return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');
    }

    public function subscribeChannel()
    {
        $via = (string) request('via');
        $channel_id = (string) request('channel_id');

        if(is_null($channel_id) || is_null($channel_id))
            return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');

        if($this->subscribeToChannel($channel_id, $via, 'kill_mail', true))
            return redirect()->back()->with('success', 'You are going to be notified about killmails.');

        return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');

    }

    public function unsubscribeChannel($channel)
    {
        $channel_id = $this->getChannelChannelId($channel,'kill_mail');

        if(is_null($channel_id))
            return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');

        if($this->unsubscribeFromChannel($channel_id, 'kill_mail'))
            return redirect()->back()->with('success', 'You are no longer going to be notified about killmails.');

        return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');

    }

    public function isSubscribed($view, $channel)
    {
        return $this->getSubscribtionStatus($channel, $view, 'kill_mail');
    }

    public function isDisabledButton($view, $channel) : bool
    {
        return $this->isDisabled($channel, $view, 'kill_mail');
    }

    public function isAvailable() : bool
    {
        return $this->hasPermission('kill_mail');
    }
}