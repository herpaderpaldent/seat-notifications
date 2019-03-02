<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken;

use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordChannel;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordMessage;
use Seat\Web\Models\Group;

/**
 * Class DiscordRefreshTokenNotification.
 * @package Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken
 */
class DiscordRefreshTokenNotification extends AbstractRefreshTokenNotification
{
    /**
     * Determine if channel has personal notification setup.
     *
     * @return bool
     */
    public static function hasPersonalNotification() : bool
    {
        return true;
    }

    public function via($notifiable)
    {
        array_push($this->tags, is_null($notifiable->group_id) ? 'to channel' : 'private to: ' . $this->getMainCharacter(Group::find($notifiable->group_id))->name);

        return [DiscordChannel::class];
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
                    ->color($this->color('danger', 'dec'))
                    ->field('Character', $character, true)
                    ->field('Corporation', $this->corporation, true)
                    ->field('Main Character', $this->main_character, false);
            });
    }
}
