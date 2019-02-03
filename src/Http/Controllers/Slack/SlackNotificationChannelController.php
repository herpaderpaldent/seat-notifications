<?php
/**
 * MIT License
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
 *
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Slack;

use Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\BaseNotificationChannelController;

class SlackNotificationChannelController extends BaseNotificationChannelController
{
    public function getSettingsView() :string
    {
        return 'seatnotifications::slack.settings';
    }

    public function getRegistrationView() :string
    {
        return 'seatnotifications::slack.registration';
    }

    public function getChannels()
    {
        if(is_null(setting('herpaderp.seatnotifications.slack.credentials.token', true)))
            return ['slack' => []];

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

        return ['slack' => $response->map(function ($item) {
            return collect([
                'name' => $item->name,
                'id' => $item->id,
                'private_channel' => $item->is_group,
            ]);
        })];
    }
}
