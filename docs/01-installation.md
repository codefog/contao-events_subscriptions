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
 - NotifcationCenter 1.3.4


### Install using Composer (recommended)

[Composer][1] is the recommended way to install Contao modules.
The Contao plugin will take care of copying the files to the correct place.

    $ composer.phar require codefog/contao-events_subscriptions ^2.0


### Install from Extension Repository (in Contao 3.5)

Events Subscriptions can also be installed from the Contao Extension Repository.
Follow the Contao manual on [how to install extensions][2].


### Manual installation

Download [`codefog/contao-events_subscriptions`][3] and copy the folder to your Contao
installation in `system/modules/events_subscriptions`. You must also download and
install [`codefog/contao-haste`][4] and [`terminal42/contao-notification_center`][5].



[1]: https://getcomposer.org
[2]: https://docs.contao.org/books/manual/3.5/en/05-system-administration/extensions.html
[3]: https://github.com/codefog/contao-events_subscriptions/archive/master.zip
[4]: https://github.com/codefog/contao-haste/archive/master.zip
[5]: https://github.com/terminal42/contao-notification_center/archive/master.zip
