<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 26.12.2018
 * Time: 15:02
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications;


use Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordUser;
use Herpaderpaldent\Seat\SeatNotifications\Models\RefreshTokenNotification;
use Illuminate\Support\Collection;
use Seat\Web\Http\Controllers\Controller;

class RefreshTokenController extends Controller
{
    public function getModel()
    {
        return (new RefreshTokenNotification());
    }

    public function getNotification()
    {
        return 'some description';
    }

    public function getPrivateView()
    {
        return view('seatnotifications::refresh_token.private')->render();
    }

    public function getChannelView()
    {
        return 'view';
    }

    public function subscribe()
    {

        $group_id = auth()->user()->group()->id;
        $discord_user = DiscordUser::find($group_id);

        RefreshTokenNotification::updateOrCreate(
            ['channel_id' => $discord_user->channel_id],
            ['type' => 'private', ]
        );

    }

}