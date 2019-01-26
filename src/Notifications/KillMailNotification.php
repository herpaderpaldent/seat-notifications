<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 23.01.2019
 * Time: 19:22
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications;




use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordChannel;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordMessage;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackChannel;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackMessage;
use Seat\Eveapi\Models\Killmails\CorporationKillmail;

class KillMailNotification extends BaseNotification
{
    /**
     * @var array
     */
    protected $tags = ['kill_mail'];

    /**
     * @var
     */
    private $killmail_id;

    /**
     * @var
     */
    private $corporation_id;

    /**
     * KillMailNotification constructor.
     *
     * @param \Seat\Eveapi\Models\Killmails\CorporationKillmail $corporation_killmail
     */
    public function __construct(CorporationKillmail $corporation_killmail)
    {

        parent::__construct();

        $this->killmail_id = $corporation_killmail->killmail_id;
        $this->corporation_id = $corporation_killmail->corporation_id;
    }

    public function via($notifiable)
    {
        var_dump('before via');

        switch ($notifiable->via) {
            case 'discord':
                /*var_dump('case discord');
                $this->tags = [
                    'kill_mail',
                    'discord',
                    $notifiable->type === 'private' ? $notifiable->recipient() : 'channel',
                ];

                var_dump($this->tags);*/

                return [DiscordChannel::class];
                break;
            case 'slack':
                $this->tags = [
                    'kill_mail',
                    'slack',
                    $notifiable->type === 'private' ? $notifiable->recipient() : 'channel',
                ];

                return [SlackChannel::class];
                break;
            default:
                return [''];

        }
    }

    public function toDiscord($notifiable)
    {

        return (new DiscordMessage)
            ->embed(function ($embed) {

                $embed->title('**Killmail**')
                    //->thumbnail($this->image)
                    ->color('16711680')
                    ->field('Character', 'test');
                    //->field('Corporation', $this->corporation, true)
                    //->field('Main Character', $this->main_character, false);
            });
    }

    /**
     * @param $notifiable
     *
     * @return \Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackMessage
     */
    /*public function toSlack($notifiable)
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
    }*/





}