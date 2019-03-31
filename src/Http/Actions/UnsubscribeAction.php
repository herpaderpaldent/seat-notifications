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

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Actions;

use Exception;
use Herpaderpaldent\Seat\SeatNotifications\Models\NotificationRecipient;
use Herpaderpaldent\Seat\SeatNotifications\Models\NotificationSubscription;

class UnsubscribeAction
{
    public function execute(array $data)
    {
        try {

            $subscription = NotificationRecipient::where('driver_id', $data['driver_id'])
                ->where('driver', $data['driver'])
                ->first()
                ->subscriptions
                ->filter(function ($subscription) use ($data) {
                    return $subscription->notification === $data['notification'];
                })
                ->first();

            NotificationSubscription::where('recipient_id', $subscription->recipient_id)
                ->where('notification', $subscription->notification)
                ->delete();

            $empty_recipient = NotificationRecipient::where('driver_id', $data['driver_id'])
                ->where('driver', $data['driver'])
                ->first()
                ->subscriptions
                ->isEmpty();

            if($empty_recipient)
                NotificationRecipient::where('driver_id', $data['driver_id'])
                    ->where('driver', $data['driver'])
                    ->first()
                    ->delete();

            return redirect()->route('seatnotifications.index')->with('success', 'You have successfully unsubscribed to ' . $data['notification']::getTitle() . ' notification.');

        } catch (Exception $e) {

            return redirect()->route('seatnotifications.index')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
