# seat-notifications
With this [SeAT](https://github.com/eveseat/seat) Package you can setup and manage notifications. It is build to be expended from within the package or from another package. Please read more about it further down.

[![Latest Stable Version](https://poser.pugx.org/herpaderpaldent/seat-notifications/v/stable)](https://packagist.org/packages/herpaderpaldent/seat-notifications)
[![StyleCI](https://github.styleci.io/repos/140680541/shield?branch=master)](https://github.styleci.io/repos/140680541)
[![Maintainability](https://api.codeclimate.com/v1/badges/2270953cdfaa22197d78/maintainability)](https://codeclimate.com/github/herpaderpaldent/seat-notifications/maintainability)
[![License](https://poser.pugx.org/herpaderpaldent/seat-notifications/license)](https://packagist.org/packages/herpaderpaldent/seat-notifications)
[![Total Downloads](https://poser.pugx.org/herpaderpaldent/seat-notifications/downloads)](https://packagist.org/packages/herpaderpaldent/seat-notifications)

***Important**: seat-notifications are work in progress and certainly have some bugs
please do report any findings to [seat-slack](https://eveseat-slack.herokuapp.com/) and report it as an issue*.

## Installation

1. cd to `/var/www/seat`
2. enter `composer require herpaderpaldent/seat-notifications`
4. run migration `php artisan migrate`

### Enable Notification Channel
To enable `seat-notifications` functionality of sending notifications. Create a bot for your notification channel. By default seat-notifications offers two notification channels which could be extended by other packages: `slack` and `discord`:
![configure](https://i.imgur.com/3ueTIaO.png)

a more detailed guide on oAuth creation will follow for now the blow table must suffice:

| Notification Channel | Redirect URLs                                                      | Comment                                                                |
|----------------------|--------------------------------------------------------------------|------------------------------------------------------------------------|
| Discord              | {seat_url}/seatnotifications/discord/configuration/callback/server | This callback url is needed for the configuration of your discord bot. |
| Discord              | {seat_url}/seatnotifications/discord/callback/user                 | This callback url is needed for the authentication of a discord user.  |
| Slack                | {seat_url}/seatnotifications/slack/configuration/callback/server   | This callback url is needed for the configuration of your slack bot.   |
| Slack                | {seat_url}/seatnotifications/slack/callback/user                   | This callback url is needed for the authentication of a slack user.    |

***Note**: you may only configure one notification channel at your will. However, for discord you must create a bot in your application. For Slack you need to add the bot permission to your oauth scope.*

### Setup Roles
To be able to subscribe to a notification the user needs the appropriate permission. Please setup a role that carries the needed permission and assign it to users that should be able to receive the notification.

### Restart workers
Since the notifications are send out by your workers you need to restart them to pick up the new code. Do this either via `docker-compose restart seat-worker` if you use docker, or `supervisorctl restart worker` on linux distributions.

### Authenticate
Users need to authenticate for your setup notification channel prior to receiving notifications. they may do this in the registration box on the notification page.

## What does the package do at the moment
* Send `RefreshTokenDeleted` Notification to Discord and/or Slack when a `refresh_token` is deleted.
* Using Model Observer to dispatch Notifications
* Notifications are queueable and send out via the queue. 
* The RefreshTokenNotification is able to be delivered to Channels or via DM on the users preference. 

Example:
![image](https://user-images.githubusercontent.com/6583519/50541121-0f8b3e00-0b9f-11e9-9319-1a4512376271.png)
![picture](https://i.imgur.com/img64u6.png)

## Developing

Most importantly: take note of [laravel's notification documentation](https://laravel.com/docs/5.5/notifications). Secondly have a look at [this package service provider](https://github.com/herpaderpaldent/seat-notifications/blob/master/src/SeatNotificationsServiceProvider.php) as it helps with merging the services array properly. The provided example of [RefreshTokenDeletedNotification](https://github.com/herpaderpaldent/seat-notifications/blob/master/src/Notifications/RefreshTokenDeletedNotification.php) is based upon it. Notifications are being send by using the `Notification` facade:

```
Notification::send($receipients, (new RefreshTokenDeletedNotification($refresh_token)));
```

where `$receipients` are a collection of modal that should receive the notification and `$refresh_token` is the deleted `refresh_token` that is passed to the constructor. In this example we use an Observer to send notifications: [Observer](https://github.com/herpaderpaldent/seat-notifications/blob/master/src/Observers/RefreshTokenObserver.php).

### Architecture

![uml_diagram](https://yuml.me/diagram/scruffy/class/[%3C%3CINotification%3E%3E%7Bbg:deepskyblue%7D]%5E-.-[AbstractNotification%7Bbg:deepskyblue%7D],[AbstractNotification]%5E[AbstractRefreshTokenNotification],[AbstractRefreshTokenNotification]%5E[DiscordRefreshTokenNotification%7Bbg:yellowgreen%7D],[AbstractRefreshTokenNotification%7Bbg:yellowgreen%7D]%5E[SlackRefreshTokenNotification%7Bbg:yellowgreen%7D],[AbstractNotification]++-%3E[%3C%3CINotificationDriver%3E%3E],[%3C%3CINotificationDriver%3E%3E]%5E-.-[DiscordNotificationDriver%7Bbg:lightseagreen%7D],[%3C%3CINotificationDriver%3E%3E%7Bbg:lightseagreen%7D]%5E-.-[SlackNotificationDriver%7Bbg:lightseagreen%7D],[AbstractNotification]uses%20-.-%3E[NotificationSubscription%7Bbg:orange%7D],[NotificationSubscription]++-%3E[NotificationRecipient%7Bbg:orange%7D],[NotificationRecipient]++-1%3E[Group%7Bbg:tomato%7D],[Group]++-%3E[User%7Bbg:tomato%7D])

### Add new drivers

If you have written a new notification driver that you would like to use for sending notifications to your users you might extend `config/services.php` similar to the provided example:

```
'seat-notification-channel' => [
        'discord'   => Herpaderpaldent\Seat\SeatNotifications\Drivers\DiscordNotificationDriver::class,
    ],
```

Your driver should extend `Herpaderpaldent\Seat\SeatNotifications\Drivers\AbstractNotificationDriver` and should contain;

```
    /**
     * The view name which will be used to store the channel settings.
     *
     * @return string
     */
    public static function getSettingsView() : string;

    /**
     * The label which will be applied to the subscription button.
     *
     * @return string
     */
    public static function getButtonLabel() : string;

    /**
     * The CSS class which have to be append into the subscription button.
     *
     * @return string
     */
    public static function getButtonIconClass() : string;

    /**
     * @return array
     */
    public static function getChannels() : array;

    /**
     * Determine if a channel has been properly setup.
     *
     * @return bool
     */
    public static function isSetup(): bool;

```

### Add new notifications

If you want to extend the available notifications you need to extend the `seat-notification` array in `config/services.php`:

```
'seat-notification'         => [
         // notification => [provider => implementation]
         Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken\AbstractRefreshTokenNotification::class => [
             'discord' => Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken\DiscordRefreshTokenNotification::class,
             'slack'   => Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken\SlackRefreshTokenNotification::class,
         ],
```

Your abstract notification must extend `Herpaderpaldent\Seat\SeatNotifications\Notifications\AbstractNotification` and contain the following methods to add your notification to the users notification list:

```
    /**
     * Return a title for the notification which will be displayed in UI notification list.
     * @return string
     */
    public static function getTitle(): string;

    /**
     * Return a description for the notification which will be displayed in UI notification list.
     * @return string
     */
    public static function getDescription(): string;

    /**
     * Determine if a notification can target public channel (forum category, chat, etc...).
     * @return bool
     */
    public static function isPublic(): bool;

    /**
     * Determine if a notification can target personal channel (private message, e-mail, etc...).
     * @return bool
     */
    public static function isPersonal(): bool;

    /**
     * Determine the permission needed to represent driver buttons.
     * @return string
     */
    public static function getPermission(): string;

```


### Use Notification Dispatcher

In order to dispatch notification solely to receiver that subscribed to the notification it is advised to use a custom dispatcher job f.e.:


````php
 public function handle()
    {
        Redis::funnel('soft_delete:refresh_token_' . $this->refresh_token->user->id)->limit(1)->then(function () {
            logger()->info('SoftDelete detected of ' . $this->refresh_token->user->name);

            $recipients = NotificationRecipient::all()
                ->filter(function ($recipient) {
                    return $recipient->shouldReceive(AbstractRefreshTokenNotification::class);
                });

            if($recipients->isEmpty()){
                logger()->debug('No Receiver found for this Notification. This job is going to be deleted.');
                $this->delete();
            }

            $recipients->groupBy('driver')
                ->each(function ($grouped_recipients) {
                    $driver = (string) $grouped_recipients->first()->driver;
                    $notification_class = AbstractRefreshTokenNotification::getDriverImplementation($driver);

                    Notification::send($grouped_recipients, (new $notification_class($this->refresh_token)));
                });

        }, function () {

            logger()->debug('A Soft-Delete job is already running for ' . $this->refresh_token->user->name);
            $this->delete();
        });
    }
````
