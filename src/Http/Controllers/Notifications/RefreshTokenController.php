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

class RefreshTokenController extends BaseNotificationController
{
    /**
     * TODO : replace by property
     *
     * @return string
     * @deprecated
     */
    public function getNotification() : string
    {
        return 'seatnotifications::refresh_token.notification';
    }

    /**
     * TODO : replace by generic built view based on channel provider unique identifier and notification unique identifier
     *
     * @return string
     * @deprecated
     */
    public function getPrivateView() : string
    {
        return 'seatnotifications::refresh_token.private';
    }

    /**
     * TODO : replace by generic built view based on channel provider unique identifier and notification unique identifier
     *
     * @return string
     * @deprecated
     */
    public function getChannelView() : string
    {
        return 'seatnotifications::refresh_token.channel';
    }

    public function subscribeDirectMessage($via)
    {
        $channel_id = $this->getPrivateChannel($via);

        if (is_null($channel_id))
            return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');

        if ($this->subscribeToChannel($channel_id, $via, 'refresh_token'))
            return redirect()->back()->with('success', 'You are going to be notified if someone deletes his refresh_token.');

        return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');
    }

    public function unsubscribeDirectMessage($via)
    {
        $channel_id = $this->getPrivateChannel($via);

        if (is_null($channel_id))
            return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');

        if ($this->unsubscribeFromChannel($channel_id, 'refresh_token'))
            return redirect()->back()->with('success', 'You are no longer going to be notified about deleted refresh_tokens.');

        return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');
    }

    public function subscribeChannel()
    {
        $via = (string) request('via');
        $channel_id = (string) request('channel_id');

        if (is_null($channel_id) || is_null($channel_id))
            return abort(500);

        if($this->subscribeToChannel($channel_id, $via, 'refresh_token', true))
            return redirect()->back()->with('success', 'Channel will receive refresh_token notifications from now on.');

        return abort(500);
    }

    public function unsubscribeChannel($channel)
    {
        $channel_id = $this->getChannelChannelId($channel, 'refresh_token');

        if (is_null($channel_id))
            return abort(500);

        if ($this->unsubscribeFromChannel($channel_id, 'refresh_token'))
            return redirect()->back()->with('success', 'Channel will not receive refresh_token notifications from this point on.');

        return abort(500);
    }

    public function isSubscribed($view, $channel)
    {
        return $this->getSubscribtionStatus($channel, $view, 'refresh_token');
    }

    public function isDisabledButton($view, $channel) : bool
    {
        return $this->isDisabled($channel, $view, 'seatnotifications.refresh_token');
    }

    /**
     * @return bool
     * @deprecated
     */
    public function isAvailable() : bool
    {
        return $this->hasPermission('seatnotifications.refresh_token');
    }
}
