<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 05.07.2018
 * Time: 15:53
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications;

use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordChannel;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordMessage;
use Herpaderpaldent\Seat\SeatNotifications\Channels\SlackWebhook\SeatSlackMessage;
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
        $this->image = "https://imageserver.eveonline.com/Character/" . $refresh_token->character_id . "_128.jpg";
        $this->main_character = $this->getMainCharacter($refresh_token->user->group)->name;
        $this->corporation = CorporationInfo::find($refresh_token->user->character->corporation_id)->name;
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
                return [DiscordChannel::class];
                break;
            /*case 'slack':
                return [SeatSlackWebhookChannel::class];
                break;*/
        }
    }

    public function toDiscord($notifiable)
    {
        var_dump('toDiscord');

        return (new DiscordMessage)
            //->content('test')
            ->embed(function ($embed) {

                $embed->title('**A SeAT users refresh token was removed!**')
                    ->thumbnail($this->image)
                    ->color('16711680')
                    ->field('Character', $this->user_name, true)
                    ->field('Corporation', $this->corporation, true)
                    ->field('Main Character',$this->main_character, false);
            });
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toDiscordWebhook($notifiable)
    {
        /*return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
        */
        //return DiscordMessage::create("You have been challenged to a game of *THIS IS A TEST**!");
       /* return (new DiscordMessage)
            ->from('SeAT Discord Webhook')
            //->color('2021216')
            //->content('**A SeAT users refresh token was removed!**')
            ->embed(function ($embed) {

                $embed->title('**A SeAT users refresh token was removed!**')
                    ->thumbnail($this->image)
                    ->color('16711680')
                    ->field('Character', $this->user_name, true)
                    ->field('Corporation', $this->corporation, true)
                    ->field('Main Character',$this->main_character, false);
            })
            ->url($this->webhook_url);*/
    }

    /**
     * @param $notifiable
     *
     * @return SeatSlackMessage
     */
    public function toSeatSlack($notifiable)
    {

        if($this->webhook_url === "")
        {
            return (new SeatSlackMessage)
                ->content('Whoops! 2 .');
        }

        return (new SeatSlackMessage)
            ->to($this->webhook_url) //let's us the table for time being as to
            //->content('**A SeAT users refresh token was removed!**')
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
            'channel' => "channel",
            'username' => "username",
        ];
    }

    public function getChannel($channel)
    {
        switch ($channel) {
            case 'discord':
                return $this->arr['discord']['channel_id'];
                break;
        }

        return '';
    }

}