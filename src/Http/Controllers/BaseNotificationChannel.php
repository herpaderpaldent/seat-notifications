<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 22.12.2018
 * Time: 21:52
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers;


use Seat\Web\Http\Controllers\Controller;

abstract class BaseNotificationChannel extends Controller
{
    protected abstract function getSettingsView();

    protected abstract function getRegistrationView();

}