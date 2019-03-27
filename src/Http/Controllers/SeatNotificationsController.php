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

use Herpaderpaldent\Seat\SeatNotifications\Exceptions\ImplementPrivateFlowException;
use Herpaderpaldent\Seat\SeatNotifications\Http\Actions\AddSubscriptionStatus;
use Herpaderpaldent\Seat\SeatNotifications\Http\Actions\Filter\GetFilterListAction;
use Herpaderpaldent\Seat\SeatNotifications\Http\Actions\GetPublicDriverId;
use Herpaderpaldent\Seat\SeatNotifications\Http\Actions\SubscribeAction;
use Herpaderpaldent\Seat\SeatNotifications\Http\Actions\UnsubscribeAction;
use Herpaderpaldent\Seat\SeatNotifications\Http\Validations\FilterRequest;
use Herpaderpaldent\Seat\SeatNotifications\Http\Validations\SubscribeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Seat\Services\Repositories\Character\Character;
use Seat\Services\Repositories\Corporation\Corporation;
use Seat\Web\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

class SeatNotificationsController extends Controller
{
    use Character;
    use Corporation;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $notification_channels = config('services.seat-notification-channel');

        return view('seatnotifications::index', compact('notification_channels'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSubscribe(SubscribeRequest $request, SubscribeAction $action, GetPublicDriverId $get_public_driver_id, UnsubscribeAction $unsubscribe_action)
    {

        /*
         * If a private subscription is being made and the user has not previously
         * setup his private driver_id.
         */
        if (empty($request->input('driver_id'))) {

            // Store relevant data for subscription to the session.
            session([
                'herpaderp.seatnotifications.subscribe.driver' => request()->input('driver'),
                'herpaderp.seatnotifications.subscribe.notification' => request()->input('notification'),
                'herpaderp.seatnotifications.subscribe.corporations_filter' => $request->input('corporations_filter'),
                'herpaderp.seatnotifications.subscribe.characters_filter' => $request->input('characters_filter'),
            ]);

            // if no private registration route is setup throw an exception.
            if ($request->input('notification')::getDriver($request->input('driver'))::getPrivateRegistrationRoute() === null)
                throw new ImplementPrivateFlowException($request->input('driver'));

            // redirect user to driver's private registration route to get private driver_id.
            return redirect()->route($request->input('notification')::getDriver($request->input('driver'))::getPrivateRegistrationRoute());
        }

        /*
         * If user updates his previously subscribed public notification
         */
        if ($request->input('public') === 'true' && ! empty($get_public_driver_id->execute($request->all()))) {

            // update driver_id with previously subscribed driver_id
            $data = $request->all();
            $data['driver_id'] = $get_public_driver_id->execute($request->all());

            // unsubscribe previous recipient
            $unsubscribe_action->execute($data);
        }

        // FirstOrCreate recipient and createOrUpdate subscription
        return $action->execute($request->all());
    }

    /**
     * @param \Illuminate\Http\Request                                               $request
     * @param \Herpaderpaldent\Seat\SeatNotifications\Http\Actions\GetPublicDriverId $get_public_driver_id
     * @param UnsubscribeAction                                                      $unsubscribe_action
     *
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function postUnsubscribe(Request $request, GetPublicDriverId $get_public_driver_id, UnsubscribeAction $unsubscribe_action)
    {

        $driver_id = $request->input('driver_id');

        if (empty($driver_id))
            $driver_id = $get_public_driver_id->execute($request->all());

        $data = [
            'driver' => $request->input('driver'),
            'driver_id' => $driver_id,
            'notification' => $request->input('notification'),
        ];

        return $unsubscribe_action->execute($data);
    }

    /**
     * @return mixed
     */
    public function getNotifications()
    {
        $notifications = $this->getNotificationCollection();

        return DataTables::of($notifications)
            ->editColumn('notification', function ($row) {
                return view('seatnotifications::partials.notification', compact('row'));
            })
            ->editColumn('personal', function ($row) {
                if (! $row::isPersonal() || ! auth()->user()->has($row::getPermission(), false))
                    return 'Not available';

                return view('seatnotifications::partials.personal', compact('row'));
            })
            ->editColumn('public', function ($row) {
                if (! $row::isPublic() || ! auth()->user()->has($row::getPermission(), false))
                    return 'Not available';

                return view('seatnotifications::partials.public', compact('row'));
            })
            ->rawColumns(['notification', 'personal', 'public'])
            ->make(true);
    }

    /**
     * @param \Illuminate\Http\Request                                                   $request
     * @param \Herpaderpaldent\Seat\SeatNotifications\Http\Actions\AddSubscriptionStatus $add_subscription_status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChannels(Request $request, AddSubscriptionStatus $add_subscription_status) : JsonResponse
    {
        // retrieve configured drivers
        $drivers = config('services.seat-notification-channel');

        // validate query parameter against valid drivers list
        $request->validate([
            'driver' => 'required|string|in:' . implode(',', array_keys($drivers)),
        ]);

        $driver = $request->query('driver');

        // return the list of channels published by the requested provider
        return $drivers[$driver]::isSetup() ? response()->json($add_subscription_status->execute($request->all(), $drivers[$driver]::getChannels())) : response()->json([]);
    }

    /**
     * @param \Herpaderpaldent\Seat\SeatNotifications\Http\Validations\FilterRequest          $filter_request
     *
     * @param \Herpaderpaldent\Seat\SeatNotifications\Http\Actions\Filter\GetFilterListAction $get_filter_list_action
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilterList(FilterRequest $filter_request, GetFilterListAction $get_filter_list_action)
    {

        $response = $get_filter_list_action->execute($filter_request->all());

        return $response->isNotEmpty() ?
             response()->json($response) : response()->json([], 400);
    }

    /**
     * Return the class list of available notification.
     * @return array
     */
    private function getNotificationCollection() : array
    {
        return array_keys(config('services.seat-notification', []));
    }
}
