<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\DataContainer;

use Contao\Database;

class CalendarContainer
{
    /**
     * Get the notifications
     *
     * @return array
     */
    public function getNotifications()
    {
        $notifications = [];
        $records       = Database::getInstance()->execute(
            "SELECT id, title FROM tl_nc_notification WHERE type='events_subscriptions_reminder' ORDER BY title"
        );

        while ($records->next()) {
            $notifications[$records->id] = $records->title;
        }

        return $notifications;
    }
}
