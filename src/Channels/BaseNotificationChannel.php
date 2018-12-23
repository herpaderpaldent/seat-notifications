<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 22.12.2018
 * Time: 21:52
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Channels;


abstract class BaseNotificationChannel
{
    protected abstract function getSettingView();

}