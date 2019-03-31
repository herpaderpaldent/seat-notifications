# Version 2.0.0
This major release revamps seat-notifications significantly. Thanks to new co-author [warlof](https://github.com/warlof), we majorly refactoring all of seat-notification in order to introduce new notifications from within this package or from a third party application with ease.

## ATTENTION: 
1. As this is a major release your subscriptions will be reset. You need to reapply your notifications.
2. Do not forget to run migrations

Please report any issues on [github](https://github.com/herpaderpaldent/seat-notifications/issues).

# Version 1.1.2
This version adds some smaller improvements:
* Delete job from dispatcher if there are no recipients for a given notification.
* KillMail Notification for channel: show selected channel
* Permission handling on BaseNotification: Full string support to support external developers
* Attempt to support NPC Killmails

Regarding NPC Killmails, this is only a first attempt and work is in process.

# Version 1.1.1
This update adds some better tagging and job handling. Also it ensures that `AbstractSeatPlugin` class present as it requires eveseat/web ~3.0.14.

# Version 1.1.0

```
Don't forget to run migrations
```

## Description
This is the first item on the changelog. This version introduces kill mail notifications. Although this seems anything but a small addition the underlying capabilities of this package was completely reworked. Instead of having every other package handling their own subscribers and logic, the `BaseNotificationController.php` class was majorly extended abstracted and refactored to a degree, where it supports any other notification controller to get rid of their own recipient handling and database logic. 

* Added Killmail Notifications
* Added ability to subscribe to multiple corporations as source of notification.
* Improved RateLimiting handling for discord: respect per channel rate-limit.
* Improved tags to monitor
* Fixed some permission issues

If you happen to find bugs please don't hesitate to ping me on [seat-slack](https://eveseat-slack.herokuapp.com/). I tested the plugin in my own corporation and concluded it to be working, however bugs happens and your feedback is a very valued source to improve.

---

For any developers that would like to take use of these capabilities please do not hesitate to reach out to me and take a look at [KillMailController.php](https://github.com/herpaderpaldent/seat-notifications/blob/master/src/Http/Controllers/Notifications/KillMailController.php) for an example with affiliations.

* Attention: only corporations are currently supported although whenever needed character affiliations could be added with ease: [SeatNotification.php](https://github.com/herpaderpaldent/seat-notifications/blob/master/src/Models/SeatNotification.php#L36)

For a simpler implementation please review [RefreshTokenController.php](https://github.com/herpaderpaldent/seat-notifications/blob/master/src/Http/Controllers/Notifications/RefreshTokenController.php)

