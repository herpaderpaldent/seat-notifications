<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 05.07.2018
 * Time: 15:53.
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

    public function __construct(RefreshToken $refresh_token)
    {
        parent::__construct();

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
        switch($notifiable->via) {
            case 'discord':
                $this->tags = [
                    'refresh_token',
                    'discord',
                    $notifiable->type === 'private' ? $notifiable->recipient() : 'channel',
                ];

                return [DiscordChannel::class];
                break;
            case 'slack':
                $this->tags = [
                    'refresh_token',
                    'slack',
                    $notifiable->type === 'private' ? $notifiable->recipient() : 'channel',
                ];

                return [SlackChannel::class];
                break;
        }
    }

    public function toDiscord($notifiable)
    {
        return (new DiscordMessage)
            ->embed(function ($embed) {

                $embed->title('**A SeAT users refresh token was removed!**')
                    ->thumbnail($this->image)
                    ->color('16711680')
                    ->field('Character', $this->user_name, true)
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
