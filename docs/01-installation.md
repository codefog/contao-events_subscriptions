# Installation – Events Subscriptions

1. [**Installation**](01-installation.md)
2. [Basic configuration](02-basics.md)
3. [Advanced configuration](03-advanced.md)
4. [Backend interface](04-backend.md)
5. [Frontend modules](05-frontend-modules.md)
6. [Notifications](06-notifications.md)
7. [Insert tags](07-insert-tags.md)
8. [Developers](08-developers.md)


## Installation

Minimum requirements:

 - Contao 3.5 or Contao 4.1
 - Haste 4.16
 - Notifcation Center 1.4


### Install using Composer (recommended)

[Composer][1] is the recommended way to install Contao modules.
The Contao plugin will take care of copying the files to the correct place.

    $ composer.phar require codefog/contao-events_subscriptions ^2.0


### Manual installation

Download [`codefog/contao-events_subscriptions`][3] and copy the folder to your Contao
installation in `system/modules/events_subscriptions`. You must also download and
install [`codefog/contao-haste`][4] and [`terminal42/contao-notification_center`][5].


## Upgrading from 1.x to 2.x

Quite a few things has changed since version 1.x of the extension but most of them will
be handled automatically by the script. However the thing you must manually adjust is the
notification reminder which has to be created in the Notification Center module. For more
information on how to do it please read the documentation.



[1]: https://getcomposer.org
[3]: https://github.com/codefog/contao-events_subscriptions/archive/master.zip
[4]: https://github.com/codefog/contao-haste/archive/master.zip
[5]: https://github.com/terminal42/contao-notification_center/archive/master.zip
