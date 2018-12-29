<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.12.2018
 * Time: 20:55.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Slack;

use Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\BaseNotificationChannel;

class SlackNotificationChannelController extends BaseNotificationChannel
{
    public function getSettingsView()
    {
        return 'seatnotifications::slack.settings';
    }

    public function getRegistrationView()
    {
        return 'seatnotifications::slack.registration';
    }

    public function getChannels()
    {
        $response = cache('herpaderp.seatnotifications.slack.channels');

        if(is_null($response)){

            $channels = app('slack')
                ->conversationsList([
                    'exclude_archived' => true,
                    'types' => 'public_channel,private_channel',
                ])
                ->getChannels();

            $response = collect();

            foreach ($channels as $channel)
                $response->push($channel);

            cache(['herpaderp.seatnotifications.slack.channels' => $response], now()->addMinutes(5));

        }

        return $response->map(function ($item) {
            return collect([
                'name' => $item->name,
                'id' => $item->id,
                'private_channel' => $item->is_group,
            ]);
        });
    }
}
