<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 11.07.2018
 * Time: 20:44
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers;

use Herpaderpaldent\Seat\SeatNotifications\Actions\Discord\AddWebhookAction;
use Herpaderpaldent\Seat\SeatNotifications\Http\Validation\AddWebhookValidation;
use Herpaderpaldent\Seat\SeatNotifications\Models\Seatnotification;
use Seat\Web\Http\Controllers\Controller;

class SeatNotificationsController extends Controller
{
    public function index()
    {
        $seat_notifications = Seatnotification::all();
        return view('seatnotifications::index', compact('seat_notifications'));

    }

    public function postWebhook(AddWebhookAction $action, AddWebhookValidation $request)
    {
        if($action->execute($request->all()))
            return redirect()->back()->with('success', 'Webhook added');

        return redirect()->back()->with('error', 'Post Webhook failed');
    }

}