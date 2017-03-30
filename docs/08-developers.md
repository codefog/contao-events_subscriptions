# Developers – Events Subscriptions

1. [Installation](01-installation.md)
2. [Basic configuration](02-basics.md)
3. [Advanced configuration](03-advanced.md)
4. [Backend interface](04-backend.md)
5. [Frontend modules](05-frontend-modules.md)
6. [Notifications](06-notifications.md)
7. [Insert tags](07-insert-tags.md)
8. [**Developers**](08-developers.md)


## Information for developers

This section is dedicated to the developers only!


## Subscription types

By default there are two subscription types available: one for the guests (non-logged-in users) and one for the
members (logged-in users). It is easily possible to add a new subscription type or override the existing one.
In order to do that simply register your type in the factory:

```php
// config/config.php
\Codefog\EventsSubscriptions\Services::getSubscriptionFactory()->add('my_type', 'MyTypeSubscription');
```

The class must implement the `Codefog\EventsSubscriptions\Subscription\SubscriptionInterface`. It can also
optionally implement other interfaces such as for the data export or notification sending. For more information
and examples please check the existing classes.

After adding the type you may also want to update the DCA and language files:

```php
// dca/tl_calendar_events_subscription.php
$GLOBALS['TL_DCA']['tl_calendar_events_subscription']['palettes']['my_type'] = '{type_legend},type,addedBy;{my_type_legend},my_type_field1';

// languages/en/tl_calendar_events_subscription.php
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['typeRef']['my_type'] = 'My Type';
```


## Hooks/events

The extension comes with several hooks/events for flexibility.

### onExport

The event is triggered when the subscriptions are being exported. The argument passed on is the instance
of the `Codefog\EventsSubscriptions\Event\ExportEvent` object.

```php
$GLOBALS['TL_HOOKS'][\Codefog\EventsSubscriptions\EventDispatcher::EVENT_ON_EXPORT][] = ['MyClass', 'onExport'];
```

### onSubscribe

The event is triggered when the member is subscribed to the event. The argument passed on is the instance
of the `Codefog\EventsSubscriptions\Event\SubscribeEvent` object.

```php
$GLOBALS['TL_HOOKS'][\Codefog\EventsSubscriptions\EventDispatcher::EVENT_ON_SUBSCRIBE][] = ['MyClass', 'onSubscribe'];
```

### onUnsubscribe

The event is triggered when the member is unsubscribed from the event. The argument passed on is the instance
of the `Codefog\EventsSubscriptions\Event\UnsubscribeEvent` object.

```php
$GLOBALS['TL_HOOKS'][\Codefog\EventsSubscriptions\EventDispatcher::EVENT_ON_UNSUBSCRIBE][] = ['MyClass', 'onUnsubscribe'];
```
