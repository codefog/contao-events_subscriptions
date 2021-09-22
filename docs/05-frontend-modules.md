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

Based on the genuine event list module enhanced with the subscription functionality. Since it is based 
on the genuine event list module it offers the same features as it's ancestor.


## Event reader subscribe

Based on the genuine event reader module enhanced with the subscription functionality. Since it is based 
on the genuine event reader module it offers the same features as it's ancestor.


## Subscribe event form

The module that should be placed on the same page as the event reader module. It displays
a subscription form for the current event. 


## User event subscriptions

This module will display the events that the user is subscribed to. Works great as a subscription
overview/history for the currently logged in user. Since it is based on the genuine event list module
it offers the same features as it's ancestor.


## CSS classes

Since version 2.8.0 all the events that member is subscribed to, will get a `subscribed` CSS class in the event modules
such as event list and calendar.

Since version 2.13 the `can-subscribe` CSS class is also added to the calendar module.


## Note on `event_` template

You may need to adjust some of the `cal_` and `event_` templates to enable the extra data and subscription form.
The extension comes by default with the `event_list_subscribe` template which works as an example
but should not be used (at least unmodified) in the production.

If you wonder what data is available for the template you can simply print it:

```php
<?php $this->dumpTemplateVars() ?>
```

Example data you can find is:

```php
$this->subscribers; // all subscribers
$this->subscribers['subscribers']; // event subscribers
$this->subscribers['subscribersParticipants']; // total number of participants of event subscribers
$this->subscribers['waitingList']; // subscribers on waiting list
$this->subscribers['waitingListParticipants']; // total number of participants of subscribers on waiting list
$this->subscriptionMaximum; // maximum subscribers limit
$this->subscriptionTypes; // detailed information about subscription types
$this->subscriptionWaitingList; // 1 – waiting list enabled; 0 – disabled
$this->subscriptionWaitingListLimit; // waiting list limit
$this->subscribeWaitingList; // true if subscribe will be made to waiting list
```

Since version 2.13 For the calendar `cal_` templates, each event should contain the similar data:

```php
<?php foreach ($day['events'] as $event): ?>
Subscribers: <?= $event['subscribers']['subscribersParticipants'] + $event['subscribers']['waitingListParticipants'] ?> / <?= $event['subscriptionMaximum'] ?>
<?php endforeach; ?>
```
