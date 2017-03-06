# Insert tags – Events Subscriptions

1. [Installation](01-installation.md)
2. [Basic configuration](02-basics.md)
3. [Advanced configuration](03-advanced.md)
4. [Backend interface](04-backend.md)
5. [Frontend modules](05-frontend-modules.md)
6. [Notifications](06-notifications.md)
7. [**Insert tags**](07-insert-tags.md)
8. [Developers](08-developers.md)


## Insert tags

Here is a list of available insert tags for this extension.

`{{events_subscriptions::total_limit}}` – displays the total events limit of the currently logged in user.

`{{events_subscriptions::total_limit_left}}` – displays the events left of the currently logged in user.

`{{events_subscriptions::period_limit::value}}` – displays the value of period limit of the currently logged in user. 

`{{events_subscriptions::period_limit::unit}}` – displays the unit of period limit of the currently logged in user.

`{{events_subscriptions::period_limit_left::value}}` – displays the value of events left for the current period 
of the currently logged in user (e.g. `3`).

`{{events_subscriptions::period_limit_left::unit}}` – displays the unit of events left for the current period 
of the currently logged in user (e.g. `month`).
