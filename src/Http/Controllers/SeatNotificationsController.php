<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 11.07.2018
 * Time: 20:44
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers;


use Herpaderpaldent\Seat\SeatNotifications\Models\Seatnotification;
use Illuminate\Support\Collection;
use Seat\Services\Settings\Seat;
use Seat\Web\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

class SeatNotificationsController extends Controller
{
    public function config()
    {

        $notification_channels = collect([]);
        $classes = config('services.seat-notification-channel');

        foreach ($classes as $class) {
            $notification_channels->push(
                (new $class)->getSettingsView()
            );
        }

        return view('seatnotifications::seatnotifications.config', compact('notification_channels'));
    }

    public function index()
    {
        $notification_channels = collect([]);
        $classes = config('services.seat-notification-channel');

        foreach ($classes as $class) {
            $notification_channels->push(
                (new $class)->getRegistrationView()
            );
        }

        return view('seatnotifications::index', compact('notification_channels'));
    }

    public function getNotifications()
    {
        $notifications = $this->getNotificationCollection();

        //dd($notifications);

        return DataTables::of($notifications)
            ->rawColumns(['private'])
            ->make(true);
    }

    private function getNotificationCollection() : Collection
    {
        $notifications = collect([]);
        $classes = config('services.seat-notification');

        foreach ($classes as $class) {
            $class = (new $class);
            $notifications->push([
                'notification' => $class->getNotification(),
                'private' => $class->getPrivateView(),
                'channel' => $class->getChannelView()
            ]);
        }

        return $notifications;
    }



}