<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * Copyright (C) 2013 Codefog
 *
 * @package events_subscriptions
 * @author  Codefog <http://codefog.pl>
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */

namespace Codefog\EventsSubscriptions;

use Contao\CalendarEventsModel;
use Contao\Database;

class EventsSubscriptions
{
    /**
     * Return true if the member can subscribe to the event
     *
     * @param int $eventId
     * @param int $memberId
     *
     * @return bool
     */
    public static function canSubscribe($eventId, $memberId)
    {
        if (($event = CalendarEventsModel::findByPk($eventId)) === null) {
            return false;
        }

        // Already subscribed to the event
        if (static::isSubscribed($eventId, $memberId)) {
            return false;
        }

        // Validate maximum number of subscriptions
        if (!static::validateMaximumSubscriptions($event)) {
            return false;
        }

        return true;
    }

    /**
     * Validate the maximum number of subscriptions
     *
     * @param CalendarEventsModel $event
     *
     * @return bool
     */
    private static function validateMaximumSubscriptions(CalendarEventsModel $event)
    {
        if (!$event->subscription_maximum) {
            return true;
        }

        $total = Database::getInstance()->prepare(
            "SELECT COUNT(*) AS total FROM tl_calendar_events_subscriptions WHERE pid=?"
        )
            ->execute($event->id)
            ->total;

        return $total < $event->subscription_maximum;
    }

    /**
     * Subscribe the member and return true on success, false otherwise
     *
     * @param int $eventId
     * @param int $memberId
     *
     * @return boolean
     */
    public static function subscribeMember($eventId, $memberId)
    {
        if (!static::isSubscribed($memberId, $memberId)) {
            $insertId = Database::getInstance()->prepare(
                "INSERT INTO tl_calendar_events_subscriptions (tstamp, pid, member) VALUES (?, ?, ?)"
            )
                ->execute(time(), $eventId, $memberId)
                ->insertId;

            if ($insertId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Unsubscribe the member and return true on success, false otherwise
     *
     * @param int $eventId
     * @param int $memberId
     *
     * @return boolean
     */
    public static function unsubscribeMember($eventId, $memberId)
    {
        if (static::isSubscribed($eventId, $memberId)) {
            $affectedRows = Database::getInstance()->prepare(
                "DELETE FROM tl_calendar_events_subscriptions WHERE pid=? AND member=?"
            )
                ->execute($eventId, $memberId)
                ->affectedRows;

            if ($affectedRows) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return true if the member is subscribed
     *
     * @param int $eventId
     * @param int $memberId
     *
     * @return boolean
     */
    public static function isSubscribed($eventId, $memberId)
    {
        $subscription = Database::getInstance()->prepare(
            "SELECT id FROM tl_calendar_events_subscriptions WHERE pid=? AND member=?"
        )
            ->limit(1)
            ->execute($eventId, $memberId);

        return $subscription->numRows ? true : false;
    }
}
