<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 30.12.2018
 * Time: 15:31.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers;

use Exception;
use Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordUser;
use Herpaderpaldent\Seat\SeatNotifications\Models\SeatNotificationRecipient;
use Herpaderpaldent\Seat\SeatNotifications\Models\Slack\SlackUser;
use Seat\Web\Http\Controllers\Controller;

abstract class BaseNotificationController extends Controller
{
    abstract public function getNotification() : string;

    abstract public function getPrivateView() : string;

    abstract public function getChannelView() : string;

    public function subscribeToChannel($channel_id, $via, $notification) : bool
    {

        try {

            SeatNotificationRecipient::firstOrCreate(
                ['channel_id' => $channel_id], ['notification_channel' => $via]
            )
                ->notifications()
                ->create(['name' => $notification]);

            return true;

        } catch (Exception $e) {

            return false;
        }
    }

    public function getPrivateChannel($via) : ?string
    {
        $channel_id = null;
        $group_id = auth()->user()->group->id;

        if ($via === 'discord')
            $channel_id = DiscordUser::find($group_id)->channel_id;

        if ($via === 'slack')
            $channel_id = SlackUser::find($group_id)->channel_id;

        return $channel_id;
    }

    /**
     * @param $channel
     * @param $view
     *
     * @return bool
     * @throws \Seat\Services\Exceptions\SettingException
     */
    public function isDisabled($channel, $view, $permission) : bool
    {
        // return false if slack has not been setup
        if($channel === 'slack') {
            if(is_null(setting('herpaderp.seatnotifications.slack.credentials.token', true)))
                return true;
        }

        // return false if discord has not been setup
        if($channel === 'discord') {
            if(is_null(setting('herpaderp.seatnotifications.discord.credentials.bot_token', true)))
                return true;
        }

        if($view === 'channel') {
            if(auth()->user()->has('seatnotifications.' . $permission, false) && auth()->user()->has('seatnotifications.configuration', false))
                return false;
        }

        if($view === 'private') {

            if(auth()->user()->has('seatnotifications.' . $permission, false)) {
                if($channel === 'discord')
                    if((new DiscordUser)->isUser(auth()->user()->group))
                        return false;

                if($channel === 'slack')
                    if((new SlackUser())->isSlackUser(auth()->user()->group))
                        return false;
            }
        }

        return true;
    }
}
