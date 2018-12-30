<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 22.12.2018
 * Time: 21:52.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;

abstract class BaseNotificationChannelController extends Controller
{
    abstract protected function getSettingsView();

    abstract protected function getRegistrationView();
}
