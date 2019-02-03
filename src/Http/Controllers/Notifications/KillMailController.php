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
