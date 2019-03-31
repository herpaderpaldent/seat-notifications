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

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications;

/**
 * Interface INotification.
 * @package Herpaderpaldent\Seat\SeatNotifications\Notifications
 */
interface INotification
{
    /**
     * Return a title for the notification which will be displayed in UI notification list.
     * @return string
     */
    public static function getTitle(): string;

    /**
     * Return a description for the notification which will be displayed in UI notification list.
     * @return string
     */
    public static function getDescription(): string;

    /**
     * Determine if a notification can target public channel (forum category, chat, etc...).
     * @return bool
     */
    public static function isPublic(): bool;

    /**
     * Determine if a notification can target personal channel (private message, e-mail, etc...).
     * @return bool
     */
    public static function isPersonal(): bool;

    /**
     * Determine the permission needed to represent driver buttons.
     * @return string
     */
    public static function getPermission(): string;

    /**
     * Return the driver class which is related to the requested ID.
     * @param $driver_id string
     * @return string
     */
    public static function getDriver(string $driver_id): string;

    /**
     * Determine if a notification has been subscribed for a specific driver.
     * @param string $driver_id
     * @return bool
     */
    public static function isSubscribed(string $driver_id): bool;

    /**
     * Return a list of implementing class foreach registered providers.
     * @return array
     */
    public static function getDiversImplementations(): array;

    /**
     * Determine a list of control filters.
     * @return string
     */
    public static function getFilters(): ?string;
}
