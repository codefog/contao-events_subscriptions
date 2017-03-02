<?php

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
