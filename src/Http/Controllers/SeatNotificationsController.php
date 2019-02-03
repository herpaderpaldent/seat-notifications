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

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers;

use Illuminate\Support\Collection;
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
        $available_channels = $this->getNotificationChannelCollection();

        return DataTables::of($notifications)
            ->editColumn('notification', function ($row) {
                if(! empty($row['private']))
                    return view($row['notification']);

                return '';
            })
            ->editColumn('private', function ($row) {

                if(! empty($row['private']))
                    return view($row['private']);

                return '';
            })
            ->editColumn('channel', function ($row) use ($available_channels) {

                if(! empty($row['channel']))
                    return view($row['channel'], compact('available_channels'));

                return '';
            })
            ->rawColumns(['notification', 'private', 'channel'])
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
                'channel' => $class->getChannelView(),
            ]);
        }

        return $notifications;
    }

    private function getNotificationChannelCollection() : Collection
    {
        $notification_channels = collect([]);
        $classes = config('services.seat-notification-channel');

        foreach ($classes as $class) {
            $notification_channels->push(
                (new $class)->getChannels()
            );
        }

        return $notification_channels;
    }
}
