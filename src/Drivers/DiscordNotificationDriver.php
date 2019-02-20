<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Drivers;

use Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordUser;

/**
 * Class DiscordChannel.
 * @package Herpaderpaldent\Seat\SeatNotifications\Http\Channels\Discord
 */
class DiscordNotificationDriver implements INotificationDriver
{
    /**
     * The view name which will be used to store the channel settings.
     *
     * @return string
     */
    public static function getSettingsView(): string
    {

        return 'seatnotifications::discord.settings';
    }

    /**
     * @return string
     */
    public static function getRegistrationView(): string
    {

        return 'seatnotifications::discord.registration';
    }

    /**
     * @return string
     */
    public static function getButtonLabel(): string
    {

        return 'Discord';
    }

    /**
     * @return string
     */
    public static function getButtonIconClass(): string
    {

        return 'fa-bullhorn';
    }

    public static function getPrivateRegistrationRoute() : string
    {
        return 'seatnotifications.register.discord';
    }

    /**
     * @return array
     */
    public static function getChannels(): array
    {

        return cache()->remember('herpaderp.seatnotifications.discord.channels', 5, function () {

            $data = collect();

            // retrieve a list of channels from the registered Discord
            $channels = app('seatnotifications-discord')
                ->guild
                ->getGuildChannels([
                    'guild.id' => (int) setting('herpaderp.seatnotifications.discord.credentials.guild_id', true),
                ]);

            // building a simple key/name list which will be return as a valid channels list
            foreach ($channels as $channel) {
                if ($channel->type !== 0)
                    continue;

                $data->push([
                    'id'              => $channel->id,
                    'name'            => $channel->name,
                    'private_channel' => false,
                ]);
            }

            return $data->all();
        });
    }

    /**
     * Determine if a channel is supporting private notifications.
     *
     * @return bool
     */
    public static function allowPersonalNotifications(): bool
    {

        return true;
    }

    /**
     * Determine if a channel is supporting public notifications.
     *
     * @return bool
     */
    public static function allowPublicNotifications(): bool
    {

        return true;
    }

    /**
     * Determine if a channel has been properly setup.
     *
     * @return bool
     */
    public static function isSetup(): bool
    {

        return ! is_null(setting('herpaderp.seatnotifications.discord.credentials.bot_token', true));
    }

    /**
     * Return driver_id of user.
     *
     * @return string
     */
    public static function getPrivateChannel(): ?string
    {

        return optional(DiscordUser::find(auth()->user()->group->id))->channel_id;
    }

    /**
     * Return driver string to pass into recipient table.
     *
     * @return string
     */
    public static function getDriverIdentifier(): string
    {
        return 'discord';
    }
}
