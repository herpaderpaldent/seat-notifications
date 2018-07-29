<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 11.07.2018
 * Time: 20:44
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers;


use Herpaderpaldent\Seat\SeatNotifications\Actions\Notifications\AddSeatNotification;
use Herpaderpaldent\Seat\SeatNotifications\Actions\SeAT\AddSlackWebhook;
use Herpaderpaldent\Seat\SeatNotifications\Http\Validation\AddSlackWebhookRequest;
use Herpaderpaldent\Seat\SeatNotifications\Http\Validation\AddSeatNotificationRequest;
use Herpaderpaldent\Seat\SeatNotifications\Models\Seatnotification;
use Seat\Services\Settings\Seat;
use Seat\Web\Http\Controllers\Controller;

class SeatNotificationsController extends Controller
{
    public function index()
    {
        $seat_notifications = Seatnotification::all();
        $slack_webhook = Seat::get('slack_webhook');

        return view('seatnotifications::index', compact('seat_notifications','slack_webhook'));

    }

    public function postSeatNotification(AddSeatNotification $action, AddSeatNotificationRequest $request)
    {
        if($action->execute($request->all()))
            return redirect()->back()->with('success', 'subscribed');

        return redirect()->back()->with('error', 'failed');
    }

    public function postSlackWebhook(AddSlackWebhookRequest $request, AddSlackWebhook $action)
    {
        if($action->execute($request->all()))
            return redirect()->back()->with('success', 'Webhook added');

        return redirect()->back()->with('error', 'Post Webhook failed');
    }

    public function removeSlackWebhook()
    {
        Seat::set('slack_webhook','');
        return redirect()->back();
    }

    public function deleteSeatNotification(string $method, string $notification)
    {
        if ($seat_notification = Seatnotification::where('notification',$notification)->where('method',$method)) {
            $seat_notification->delete();
            return redirect()->back()->with('success', 'unsubscribed');
        }

        return redirect()->back()->with('error', 'failed');

    }

}