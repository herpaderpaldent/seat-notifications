<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 26.12.2018
 * Time: 15:02
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications;


use Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Discord\DiscordServerController;
use Herpaderpaldent\Seat\SeatNotifications\Http\Validation\AddRefreshTokenChannelSubscriptionRequest;
use Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordUser;
use Herpaderpaldent\Seat\SeatNotifications\Models\RefreshTokenNotification;
use Seat\Web\Http\Controllers\Controller;

class RefreshTokenController extends Controller
{
    /*public function getModel()
    {
        return (new RefreshTokenNotification());
    }*/

    public function getNotification()
    {
        return view('seatnotifications::refresh_token.notification')->render();
    }

    public function getPrivateView()
    {
        return view('seatnotifications::refresh_token.private')->render();
    }

    public function getChannelView()
    {
        // Must be do like this it is not possible to render the escaped html properly especially the needed jquery.
        // maybe this can be improved in the future.
        $response = collect((new DiscordServerController)->getChannels());

        return view('seatnotifications::refresh_token.channel', compact('response'))->render();;
    }

    public function subscribeDm($via)
    {
        $group_id = auth()->user()->group->id;
        $channel_id = null;

        if($via === 'discord')
            $channel_id = DiscordUser::find($group_id)->channel_id;

        if(is_null($channel_id))
            return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');

        RefreshTokenNotification::updateOrCreate(
            ['channel_id' => $channel_id],
            ['type' => 'private', 'via' => $via]
        );

        if($via === 'discord')
            setting(['herpaderp.seatnotifications.refresh_token.status.discord', 'subscribed']);

        return redirect()->back()->with('success', 'You are going to be notified if someone deletes his refresh_token.');
    }

    public function unsubscribeDm($via)
    {
        $group_id = auth()->user()->group->id;
        $channel_id = null;

        if($via === 'discord')
            $channel_id = DiscordUser::find($group_id)->channel_id;

        if(is_null($channel_id))
            return redirect()->back()->with('error', 'Something went wrong, please assure you have setup your personal delivery channel correctly.');

        RefreshTokenNotification::destroy($channel_id);

        if($via === 'discord')
            setting(['herpaderp.seatnotifications.refresh_token.status.discord', 'unsubscribed']);

        return redirect()->back()->with('success', 'You are no longer going to be notified if someone deletes his refresh_token.');

    }

    public function subscribeChannel(AddRefreshTokenChannelSubscriptionRequest $request)
    {
        RefreshTokenNotification::updateOrCreate(
            ['channel_id' => $request->input('channel_id')],
            ['type' => 'channel', 'via' => $request->input('via')]
        );

        if($request->input('via') === 'discord')
            setting(['herpaderp.seatnotifications.refresh_token.channel.discord', 'subscribed'], true);

        return redirect()->back()->with('success', 'Channel will receive refresh_token notifications from now on.');
    }

    public function unsubscribeChannel($via)
    {
        RefreshTokenNotification::where('via', $via)
            ->where('type','channel')
            ->delete();

        if($via === 'discord')
            setting(['herpaderp.seatnotifications.refresh_token.channel.discord', 'unsubscribed'], true);

        return redirect()->back()->with('success', 'Channel will not receive refresh_token notifications from this point on.');
    }

}