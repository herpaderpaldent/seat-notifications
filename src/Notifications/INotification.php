<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications;

use Herpaderpaldent\Seat\SeatNotifications\Drivers\INotificationDriver;

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
     * Return the driver class which is related to the requested ID
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
}
