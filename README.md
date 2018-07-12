# seat-notifications
With this [SeAT](https://github.com/eveseat/seat) Package you can setup and manage notifications. This is a poc to illustrate what i meant by "Notification package needs some love". This is more of a walking skeleton then a well designed and developed solution. But from here things could take on.

## What does the package do at the moment
* Send `RefreshTokenDeleted` Notification to Discord Webhook when a Refresh Token is deleted.
* Set Discord Webhook via Form field
* Using Model Observer to dispatch Notifications
* notifications are quable and send out via Horizon. 

## What is it i try to achive

![Overview](https://i.imgur.com/kenV6fi.png) 

* The basic idea derived from the webhook ability of slack or email per se to send individual notifications. I understood if we could enable users to save their SlackID and extend the current `toSlack()` method with the `to(ID)` method, the user setting up the notification would receive private messages containing the content of the notification. 
This would then have been very useful f.e. if someone runs industry or PI. The user would have been notified when his/her extractor runs out.

* The second idea i was aiming to achieve was: Using model observer to dispatch notifications. 

* Finally: i found the notificiation package not intuitive to read and understand. This is why i recreated it in a more (for me) intuitive and readable way, which hopefully lead to more maintainable code.

## What would the next steps be

1. Support more channels
2. create proper view with post methods and actions per type of notification: `character`, `corporation`, `seat`
3. create more observer
4. create more notifications
5. remove the test command (currently webhook on test is hard coded)
6. add permissions.
7. global setup for admins per corporation/seat to setup slack-hook.
8. Show character notifications per character owned by user-group.

