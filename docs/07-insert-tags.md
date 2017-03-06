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

1. `{{events_subscriptions::total_limit}}` 
    
   Displays the total events limit of the currently logged in user.

2. `{{events_subscriptions::total_limit_left}}`
   
   Displays the events left of the currently logged in user.

3. `{{events_subscriptions::period_limit::value}}`

   Displays the value of period limit of the currently logged in user. 

4. `{{events_subscriptions::period_limit::unit}}` 
    
   Displays the unit of period limit of the currently logged in user.

5. `{{events_subscriptions::period_limit_left::value}}`

   Displays the value of events left for the current period of the currently logged in user (e.g. `3`).

6. `{{events_subscriptions::period_limit_left::unit}}`

   Displays the unit of events left for the current period of the currently logged in user (e.g. `month`).
