<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 30.12.2018
 * Time: 15:31.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;

abstract class BaseNotificationController extends Controller
{
    abstract public function getNotification() : string;

    abstract public function getPrivateView() : string;

    abstract public function getChannelView() : string;
}
