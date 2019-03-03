<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Drivers;

use Herpaderpaldent\Seat\SeatNotifications\Models\Slack\SlackUser;

class SlackNotificationDriver implements INotificationDriver
{
    /**
     * The view name which will be used to store the channel settings.
     *
     * @return string
     */
    public static function getSettingsView(): string
    {
        return 'seatnotifications::slack.settings';
    }

    /**
     * @return string
     */
    public static function getButtonLabel() : string
    {
        return 'Slack';
    }

    /**
     * @return string
     */
    public static function getButtonIconClass() : string
    {
        return 'fa-slack';
    }

    /**
     * @return array
     */
    public static function getChannels(): array
    {
        return cache()->remember('herpaderp.seatnotifications.slack.channels', 5, function () {

            $response = collect();

            // retrieve a list of channels from the registered Slack
            $channels = app('slack')
                ->conversationsList([
                    'exclude_archived' => true,
                    'types' => 'public_channel,private_channel',
                ])
                ->getChannels();

            foreach ($channels as $channel)
                $response->push([
                    'name'            => $channel->name,
                    'id'              => $channel->id,
                    'private_channel' => $channel->is_group,
                ]);

            return $response;
        });
    }

    /**
     * Determine if a channel is supporting private notifications.
     *
     * @return bool
     */
    public static function allowPersonalNotifications(): bool
    {
        return false;
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
        return ! is_null(setting('herpaderp.seatnotifications.slack.credentials.token', true));
    }

    /**
     * Return driver_id of user.
     *
     * @return string
     */
    public static function getPrivateChannel() : ?string
    {
        return SlackUser::find(auth()->user()->group->id)->channel_id;
    }

    /**
     * Return driver_id of public subscription.
     *
     * @param string $notification
     *
     * @return string
     */
    public static function getPublicDriverId(string $notification) : ?string
    {
        return null;
    }

    /**
     * Return the route key which have to be used in a private notification registration flow.
     *
     * @return string
     */
    public static function getPrivateRegistrationRoute(): ?string
    {
        return null;
    }
}
