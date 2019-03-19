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

use Exception;
use Herpaderpaldent\Seat\SeatNotifications\Models\NotificationRecipient;

abstract class AbstractNotificationDriver implements INotificationDriver
{
    /**
     * Determine if a channel is supporting private notifications.
     *
     * @return bool
     */
    public static function allowPersonalNotifications(): bool
    {
        return false;
    }

    /**
     * Determine if a channel is supporting public notifications.
     *
     * @return bool
     */
    public static function allowPublicNotifications(): bool
    {
        return false;
    }

    /**
     * Return channel_id of user.
     *
     * @return string
     */
    public static function getPrivateChannel(): ?string
    {
        return null;
    }

    /**
     * Return the route key which have to be used in a private notification registration flow.
     *
     * @return string
     */
    public static function getPrivateRegistrationRoute(): ?string
    {
        return null;
    }

    /**
     * Return driver_id of public subscription.
     *
     * @param string $notification
     *
     * @return string
     */
    public static function getPublicDriverId(string $notification) : ?string
    {
        try {

            // get driver
            $driver = key(array_filter(config('services.seat-notification-channel'), function ($value) {
                return $value == get_called_class();
            }));

            return NotificationRecipient::where('driver', $driver)
                ->where('group_id', null)
                ->get()
                ->map(function ($recipient) use ($notification) {
                    return $recipient
                        ->subscriptions
                        ->filter(function ($subscription) use ($notification) {
                            return $subscription->notification === $notification;
                        });
                })
                ->flatten()->first()->recipient->driver_id;
        } catch (Exception $e) {

            return null;
        }
    }
}
