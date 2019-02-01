<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 26.12.2018
 * Time: 15:02.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications;

use Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\BaseNotificationController;
use Seat\Services\Repositories\Corporation\Corporation;

/**
 * Class KillMailController.
 * @package Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications
 */
class KillMailController extends BaseNotificationController
{
    use Corporation;

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

        if($this->isSubscribed('channel', $via)){

            $channel_id = $this->getChannelChannelId($via, 'kill_mail');

            if(! $this->unsubscribeFromChannel($channel_id, 'kill_mail'))
                return abort(500);
        }

        $channel_id = (string) request('channel_id');
        $affiliation = collect(['corporations' => request('corporation_ids')])->toJson();

        if(empty($channel_id) || empty($affiliation))
             abort(500);

        if($this->subscribeToChannel($channel_id, $via, 'kill_mail', true, $affiliation))
            return redirect()->back()->with('success', 'You are going to be notified about killmails.');

        return abort(500);

    }

    public function unsubscribeChannel($channel)
    {
        $channel_id = $this->getChannelChannelId($channel, 'kill_mail');

        if(is_null($channel_id))
            return abort(500);

        if($this->unsubscribeFromChannel($channel_id, 'kill_mail'))
            return redirect()->back()->with('success', 'You are no longer going to be notified about killmails.');

        return abort(500);

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

    public function getAvailableCorporations($view, $channel)
    {
        return $this->getCorporations($view, $channel, 'kill_mail');
    }
}
