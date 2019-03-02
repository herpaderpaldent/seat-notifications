# seat-notifications
With this [SeAT](https://github.com/eveseat/seat) Package you can setup and manage notifications. It is build to be expendend from within the package or from another package. Please read more about it further down.

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

### Add new channels

If you have written a new notification channel that you would like to use for sending notifications to your users you might extend `config/services.php` similar to the provided example:

```
'seat-notification-channel' => [
        'discord'   => Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Discord\DiscordNotificationChannel::class,
    ],
```

Your channel should extend [BaseNotificationChannel](https://github.com/herpaderpaldent/seat-notifications/blob/master/src/Http/Controllers/BaseNotificationChannel.php)

```
namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers;
use Seat\Web\Http\Controllers\Controller;
abstract class BaseNotificationChannel extends Controller
{
    abstract protected function getSettingsView();
    abstract protected function getRegistrationView();
}
```

### Add new notifications

If you want to extend the available notifications you need to extend the `seat-notification` array in `config/services.php`:

```
'seat-notification' => [
        'refresh_token' => Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications\RefreshTokenController::class,
    ],
```

Your custom notification must contain the following public functions to add your notification to the users notification list:

```
public function getNotification()
    {
        return 'seatnotifications::refresh_token.notification';
    }
    public function getPrivateView()
    {
        return 'seatnotifications::refresh_token.private';
    }
    public function getChannelView()
    {
        return 'seatnotifications::refresh_token.channel';
    }
```

the functions should return a string containing the name of your view that should be rendered per column.

### Use BaseNotificationController

You are absolutely free to handle your own database and recipient logic. However seat-notifications offer you a convinient and easy way to handle your subscribors in [BaseNotificationController.php](https://github.com/herpaderpaldent/seat-notifications/blob/master/src/Http/Controllers/BaseNotificationController.php)

f.e:

````php
public function subscribeToChannel($channel_id, $via, $notification, $is_channel = false, $affiliation = null) : bool
    {
        try {
            SeatNotificationRecipient::firstOrCreate(
                ['channel_id' => $channel_id], ['notification_channel' => $via, 'is_channel' => $is_channel]
            )
                ->notifications()
                ->create(['name' => $notification, 'affiliation' => $affiliation]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
````
