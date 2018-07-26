<?php

namespace Herpaderpaldent\Seat\SeatNotifications;

use Herpaderpaldent\Seat\SeatNotifications\Channels\DiscordWebhook\DiscordWebhookMessage;
use Herpaderpaldent\Seat\SeatNotifications\Channels\DiscordWebhook\DiscordWebhookChannel;
use Herpaderpaldent\Seat\SeatNotifications\Channels\SlackWebhook\SlackWebhookChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\RefreshToken;
use Carbon\Carbon;
use Seat\Services\Settings\Seat;

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

    public $method;

    public function __construct(RefreshToken $refresh_token, String $webhook_url, String $method)
    {
        $this->user_name = $refresh_token->user->name;
        $this->image = "https://imageserver.eveonline.com/Character/" . $refresh_token->character_id . "_128.jpg";
        $this->main_character = $refresh_token->user->group->main_character->name;
        $this->corporation = CorporationInfo::find($refresh_token->user->character->corporation_id)->name;
        $this->webhook_url = $webhook_url; //TODO: probabl rename to 'To'
        $this->method = $method;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        switch($this->method) {
            case 'discord':
                return [DiscordWebhookChannel::class];
                break;
            case 'slack':
                return [SlackWebhookChannel::class];
                break;
        }
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

    public function toSlack($notifiable)
    {

        if(!empty($this->webhook_url))
        {
            return (new SlackMessage)
                ->content('Whoops! .');
        }

        return (new SlackMessage)
            ->to($this->webhook_url) //let's us the table for time being as to
            ->content('**A SeAT users refresh token derp was removed!**')
            /*->attachment(function ($attachment) {
                $attachment->title('Invoice 1322', 'Derp')
                    ->fields([
                        'Title' => 'Test',
                        'Amount' => '$1,234',
                        'Via' => 'American Express',
                        'Was Overdue' => ':-1:',
                    ]);
            })*/;


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