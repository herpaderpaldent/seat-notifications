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

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications;

use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordChannel;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordMessage;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackChannel;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackMessage;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\RefreshToken;

class RefreshTokenDeletedNotification extends BaseNotification
{
    /**
     * @var array
     */
    protected $tags = ['refresh_token'];

    public $user_name;

    public $image;

    public $main_character;

    public $corporation;

    private $refresh_token;

    public function __construct(RefreshToken $refresh_token)
    {
        parent::__construct();

        $this->refresh_token = $refresh_token;
        $this->user_name = $refresh_token->user->name;
        $this->image = 'https://imageserver.eveonline.com/Character/' . $refresh_token->character_id . '_128.jpg';
        $this->main_character = $this->getMainCharacter($refresh_token->user->group)->name;
        $this->corporation = optional(CorporationInfo::find($refresh_token->user->character->corporation_id))->name ?: 'NPC Corporation';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        switch($notifiable->notification_channel) {
            case 'discord':
                $this->tags = [
                    'refresh_token',
                    'discord',
                    $notifiable->is_channel ? 'channel' : $notifiable->recipient(),
                ];

                return [DiscordChannel::class];
                break;
            case 'slack':
                $this->tags = [
                    'refresh_token',
                    'slack',
                    $notifiable->is_channel ? 'channel' : $notifiable->recipient(),
                ];

                return [SlackChannel::class];
                break;
            default:
                return [''];
        }
    }

    public function toDiscord($notifiable)
    {
        $character = sprintf('[%s](%s)',
            $this->user_name,
            route('configuration.users.edit', ['user_id' => $this->refresh_token->character_id]));

        return (new DiscordMessage)
            ->embed(function ($embed) use ($character) {

                $embed->title('**A SeAT users refresh token was removed!**')
                    ->thumbnail($this->image)
                    ->color('16711680')
                    ->field('Character', $character, true)
                    ->field('Corporation', $this->corporation, true)
                    ->field('Main Character', $this->main_character, false);
            });
    }

    /**
     * @param $notifiable
     *
     * @return \Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->warning()
            ->attachment(function ($attachment) {
                $attachment->title('RefreshTokenDeleted')
                    ->fields([
                        'Character' => $this->user_name,
                        'Corporation' => $this->corporation,
                        'Main Character' => $this->main_character,
                    ])
                ->thumb($this->image);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'channel' => 'channel',
            'username' => 'username',
        ];
    }

    /*
     * TODO: If no exception happens delet this.
     *
     * public function getChannel($channel)
    {
        switch ($channel) {
            case 'discord':
                return $this->arr['discord']['channel_id'];
                break;
        }

        return '';
    }*/

}
