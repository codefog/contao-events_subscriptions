# Notifications – Events Subscriptions

1. [Installation](01-installation.md)
2. [Basic configuration](02-basics.md)
3. [Advanced configuration](03-advanced.md)
4. [Backend interface](04-backend.md)
5. [Frontend modules](05-frontend-modules.md)
6. [**Notifications**](06-notifications.md)
7. [Insert tags](07-insert-tags.md)
8. [Developers](08-developers.md)


## Notification types and their purposes

The notifications are handled by the
[Notification Center](https://github.com/terminal42/contao-notification_center) extension.

1. **Event reminder**

   This notification type is used to send the reminders about the event. You must choose it in the
   calendar settings if you have enabled the reminders.

2. **Event subscribe**

   This notification type is used to confirm the member subscribed to the event. This notification
   does not need to be chosen anywhere, all of this notification types will be sent upon subscription.
   **Note:** this notification is also sent when the user is subscribed to the event in the backend!

3. **Event unsubscribe**

   This notification type is used to confirm the member unsubscribed from the event. This notification
   does not need to be chosen anywhere, all of this notification types will be sent upon unsubscription.
   **Note:** this notification is also sent when the user is unsubscribed from the event in the backend!

4. **Event waiting list promotion**

   This notification type is used to update the member waiting list status. It is sent only if the subscription
   promotes from a waiting list to the subscriber list due to an unsubscription of a different member.
   **Note:** this notification is also sent when the other user is unsubscribed from the event in the backend!


## Available tokens

The list of available tokens in every notification:

1. `admin_email`

   The e-mail address of administrator.

2. `recipient_email`

   The e-mail address of the subscribed user.

3. `subscription_*`

   All data of the subscribed user. Enter the field name in place of asterisk to get
   the desired data, e.g. `subscription_firstname` to get the member's firstname.

4. `subscription_waitingList`

   True if the subscriber is on the waiting list, false otherwise.

4. `event_*`

   All data of the event. Enter the field name in place of asterisk to get
   the desired data, e.g. `event_startDate` to get the event's start date.

5. `calendar_*`

   All data of the event's calendar. Enter the field name in place of asterisk to get
   the desired data, e.g. `calendar_title` to get the calendar's title.

6. `unsubscribe_link`

   A unique link that allows the user to immediately unsubscribe from the event.
   
7. `{{event_url::##event_id##}}` or `{{event::##event_id##}}`

   A link to the event itself.

