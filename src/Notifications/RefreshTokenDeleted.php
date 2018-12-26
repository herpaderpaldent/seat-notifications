<?php

namespace Herpaderpaldent\Seat\SeatNotifications;

use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordChannel;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordMessage;
use Herpaderpaldent\Seat\SeatNotifications\Channels\SlackWebhook\SeatSlackMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\RefreshToken;


/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 05.07.2018
 * Time: 15:53
 */

class RefreshTokenDeleted extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user_name;

    public $image;

    public $main_character;

    public $corporation;

    public $webhook_url;

    public $method;

    protected $arr;

    public function __construct(RefreshToken $refresh_token, array $arr)
    {
        $this->user_name = $refresh_token->user->name;
        $this->image = "https://imageserver.eveonline.com/Character/" . $refresh_token->character_id . "_128.jpg";
        $this->main_character = $refresh_token->user->group->main_character->name;
        $this->corporation = CorporationInfo::find($refresh_token->user->character->corporation_id)->name;
        $this->queue = 'high';
        $this->arr = $arr;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {

        return [DiscordChannel::class];

        /*switch($this->method) {
            case 'discord':
                return [DiscordWebhookChannel::class];
                break;
            case 'slack':
                return [SeatSlackWebhookChannel::class];
                break;
        }*/
    }

    public function toDiscord($notifiable)
    {

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