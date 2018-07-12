<?php

namespace Herpaderpaldent\Seat\SeatNotifications;

use Herpaderpaldent\Seat\SeatNotifications\Channels\DiscordWebhook\DiscordWebhookMessage;
use Herpaderpaldent\Seat\SeatNotifications\Channels\DiscordWebhook\DiscordWebhookChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\RefreshToken;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 05.07.2018
 * Time: 15:53
 */

class RefreshTokenDeleted extends Notification implements ShouldQueue //TODO: uncomment ShouldQueue
{
    use Queueable;

    /*public $connection = 'redis';

    public $queue = 'urgent';

    public $delay = 60;*/

    public $user_name;

    public $image;

    public $main_character;

    public $corporation;

    public $webhook_url;

    public function __construct(RefreshToken $refresh_token, String $webhook_url)
    {
        $this->user_name = $refresh_token->user->name;
        $this->image = "https://imageserver.eveonline.com/Character/" . $refresh_token->character_id . "_128.jpg";
        $this->main_character = $refresh_token->user->group->main_character->name;
        $this->corporation = CorporationInfo::find($refresh_token->user->character->corporation_id)->name;
        $this->webhook_url = $webhook_url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [DiscordWebhookChannel::class];
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
        return (new DiscordWebhookMessage)
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
            ->url($this->webhook_url);
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

}