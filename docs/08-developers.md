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

The extension comes with several hooks for flexibility.

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
