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
        return $this->getNotificationsByType('events_subscriptions_reminder');
    }

    /**
     * Get the subscribe notifications
     *
     * @return array
     */
    public function getSubscribeNotifications()
    {
        return $this->getNotificationsByType('events_subscriptions_subscribe');
    }

    /**
     * Get the unsubscribe notifications
     *
     * @return array
     */
    public function getUnsubscribeNotifications()
    {
        return $this->getNotificationsByType('events_subscriptions_unsubscribe');
    }

    /**
     * Get the list update notifications
     *
     * @return array
     */
    public function getListUpdateNotifications()
    {
        return $this->getNotificationsByType('events_subscriptions_listUpdate');
    }

    /**
     * Get the notifications by type
     *
     * @param string $type
     *
     * @return array
     */
    private function getNotificationsByType($type)
    {
        $notifications = [];
        $records = Database::getInstance()->prepare("SELECT id, title FROM tl_nc_notification WHERE type=? ORDER BY title")->execute($type);

        while ($records->next()) {
            $notifications[$records->id] = $records->title;
        }

        return $notifications;
    }
}
