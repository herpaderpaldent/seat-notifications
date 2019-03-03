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
use Herpaderpaldent\Seat\SeatNotifications\Http\Actions\SubscribeAction;
use Herpaderpaldent\Seat\SeatNotifications\Http\Actions\UnsubscribeAction;
use Herpaderpaldent\Seat\SeatNotifications\Http\Validations\SubscribeRequest;
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
    public function postSubscribe(SubscribeRequest $request, SubscribeAction $action)
    {

        if (empty($request->input('driver_id'))) {
            session([
                'herpaderp.seatnotifications.subscribe.driver' => request()->input('driver'),
                'herpaderp.seatnotifications.subscribe.notification' => request()->input('notification'),
                'herpaderp.seatnotifications.subscribe.corporations_filter' => $request->input('corporations_filter'),
                'herpaderp.seatnotifications.subscribe.characters_filter' => $request->input('characters_filter'),
            ]);

            if ($request->input('notification')::getDriver($request->input('driver'))::getPrivateRegistrationRoute() === null)
                throw new ImplementPrivateFlowException($request->input('driver'));

            return redirect()->route($request->input('notification')::getDriver($request->input('driver'))::getPrivateRegistrationRoute());
        }

        return $action->execute($request->all());
    }

    /**
     * @param UnsubscribeAction $unsubscribe_action
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function getUnsubscribe(UnsubscribeAction $unsubscribe_action)
    {

        $data = [
            'driver' => request()->query('driver'),
            'group_id' => request()->query('is_public') ? null : auth()->user()->group_id,
            'notification' => request()->query('notification'),
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
     * @param string $driver_id
     * @return array
     */
    public function getChannels(Request $request) : array
    {
        // retrieve configured drivers
        $drivers = config('services.seat-notification-channel');

        // validate query parameter against valid drivers list
        $request->validate([
            'driver' => 'required|string|in:' . implode(',', array_keys($drivers)),
        ]);

        $driver_id = $request->query('driver');

        // return the list of channels published by the requested provider
        return $drivers[$driver_id]::isSetup() ? $drivers[$driver_id]::getChannels() : [];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilterList(Request $request)
    {
        $request->validate([
            'filter' => 'required|string|in:characters,corporations',
        ]);

        switch ($request->input('filter')) {
            case 'characters':
                return response()->json($this->getAllCharactersWithAffiliations(false)
                                             ->select('character_id', 'name')
                                             ->orderBy('name')
                                             ->get()
                                             ->map(function ($character) {
                                                 return [
                                                     'id'   => $character->character_id,
                                                     'name' => $character->name,
                                                 ];
                                             }));
            case 'corporations':
                return response()->json($this->getAllCorporationsWithAffiliationsAndFilters(false)
                                             ->select('corporation_id', 'name')
                                             ->orderBy('name')
                                             ->get()
                                             ->map(function ($corporation) {
                                                 return [
                                                     'id'   => $corporation->corporation_id,
                                                     'name' => $corporation->name,
                                                 ];
                                             }));
        }

        return response()->json([], 400);
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
