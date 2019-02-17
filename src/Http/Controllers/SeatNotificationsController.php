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
        $notification_channels = config('services.seat-notification-channel');

        return view('seatnotifications::index', compact('notification_channels'));
    }

    public function getNotifications()
    {
        $notifications = $this->getNotificationCollection();
        //$available_channels = $this->getNotificationChannelCollection();

        return DataTables::of($notifications)
            ->editColumn('notification', function ($row) {
                return view('seatnotifications::partials.notification', compact('row'));
            })
            ->editColumn('personal', function ($row) {
                if (! $row::isPersonal())
                    return 'Not available';

                return view('seatnotifications::partials.personal', compact('row'));
            })
            ->editColumn('public', function ($row) {
                if (! $row::isPublic())
                    return 'Not available';

                return view('seatnotifications::partials.public', compact('row'));
            })
            ->rawColumns(['notification', 'personal', 'public'])
            ->make(true);
    }

    /**
     * Return the class list of available notification
     * @return array
     */
    private function getNotificationCollection() : array
    {
        return array_keys(config('services.seat-notification', []));
    }

    /**
     * @return Collection
     */
    private function getNotificationChannelCollection() : Collection
    {
        $notification_channels = collect();
        $classes = config('services.seat-notification-channel');

        // for each registered provider, retrieve available channels
        foreach ($classes as $key => $class) {
            $provider = new $class;

            // ensure the provider is properly setup
            // otherwise, skip the entry
            if (! $provider::isSetup())
                continue;

            // append the provider channels to the available channels list
            $notification_channels->push([
                $key => $provider->getChannels(),
            ]);
        }

        return $notification_channels;
    }
}
