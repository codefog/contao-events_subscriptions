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

**Event reminder** – this notification type is used to send the reminders about the event.

**Event subscribe** – this notification type is used to confirm the member subscribed to the event.
Note: this notification is also sent when the user is subscribed to the event in the backend!

**Event unsubscribe** – this notification type is used to confirm the member unsubscribed from the event.
Note: this notification is also sent when the user is unsubscribed from the event in the backend!


## Available tokens

The list of available tokens in every notification:

`admin_email` – the e-mail address of administrator.

`member_email` – the e-mail address of the subscribed member.

`member_*` – all data of the subscribed member. Enter the field name in place of asterisk to get
the desired data, e.g. `member_firstname` to get the member's firstname.

`event_*` – all data of the event. Enter the field name in place of asterisk to get
the desired data, e.g. `event_startDate` to get the event's start date.

`calendar_*` – all data of the event's calendar. Enter the field name in place of asterisk to get
the desired data, e.g. `calendar_title` to get the calendar's title.
