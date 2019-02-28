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

class SubscribeAction
{
    public function execute(array $data)
    {
        $driver = $data['driver'];
        $driver_id = $data['driver_id'];
        $notification = $data['notification'];
        $group_id = array_key_exists('group_id', $data) ? $data['group_id'] : null;

        // retrieve filters and merge them together
        $characters_filter = [0];
        $corporations_filter = [0];

        if (array_key_exists('characters_filter', $data))
            $characters_filter = $data['characters_filter'] ?: [0];

        if (array_key_exists('corporations_filter', $data))
            $corporations_filter = $data['corporations_filter'] ?: [0];

        $affiliations = json_encode([
            'characters' => $characters_filter,
            'corporations' => $corporations_filter,
        ]);

        try {

            // create a subscription
            NotificationRecipient::firstOrCreate(
                ['driver_id' => $driver_id, 'driver' => $driver], ['group_id' => $group_id]
            )
                ->subscriptions()
                ->create([
                    'notification' => $notification,
                    'affiliations' => $affiliations,
                ]);

            return redirect()->route('seatnotifications.index')->with('success', 'You have successfully subscribed to ' . $notification::getTitle() . ' notification.');

        } catch (Exception $e) {

            return redirect()->route('seatnotifications.index')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
