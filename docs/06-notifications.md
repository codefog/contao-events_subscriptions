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

5. **Event notification**

   This notification type is used when a backend user manually clicks the "Send a new event notification" button next
   to an event in the backend list module. It can mostly be used as a message to your users encouraging to subscribe
   to a new event which you have just created. The notification will address all selected members that have NOT
   subscribed to the event yet!
   

## Custom notifications per calendar
   
As of version 2.8.0 it is possible to set custom notifications per each calendar. In order to do that, simply enable
the subscriptions in the calendar settings and choose the appropriate notifications in the corresponding fields 
(e.g. `Subscribe notification`). If no subscription is chosen, then all subscriptions of a certain type will be 
sent out (standard behavior)!


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

5. `event_*`

   All data of the event. Enter the field name in place of asterisk to get
   the desired data, e.g. `event_startDate` to get the event's start date.

6. `calendar_*`

   All data of the event's calendar. Enter the field name in place of asterisk to get
   the desired data, e.g. `calendar_title` to get the calendar's title.

7. `unsubscribe_link`

   A unique link that allows the user to immediately unsubscribe from the event.
   
8. `event_link`

   A link to the event itself.

9. `days_before_event`

   A number of days before the event, `0` if the event is past.
