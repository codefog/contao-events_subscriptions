# Frontend modules – Events Subscriptions

1. [Installation](01-installation.md)
2. [Basic configuration](02-basics.md)
3. [Advanced configuration](03-advanced.md)
4. [Backend interface](04-backend.md)
5. [**Frontend modules**](05-frontend-modules.md)
6. [Notifications](06-notifications.md)
7. [Insert tags](07-insert-tags.md)
8. [Developers](08-developers.md)


## Event list subscribe

Based on the genuine event list module enhanced with the subscription functionality. It shows
all future events only, as you cannot subscribe to the past events.


## Subscribe event form

The module that should be placed on the same page as the event reader module. It displays
a subscription form for the current event. 


## User event subscriptions

This module will display the events that the user is subscribed to. Works great as a subscription
overview/history for the currently logged in user. Since it is based on the genuine event list modules
it offers all features as it's ancestor.


## Event template

You may need to adjust some of the `event_` templates to enable the extra data and subscription form.
The extension comes by default with the `event_list_subscribe` template which works as an example
but should not be used (at least unmodified) in the production.

If you wonder what data is available for the template you can simply print it:

```php
<?php dump($this->getData()) ?>
```
