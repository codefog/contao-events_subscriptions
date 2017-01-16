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

use Contao\Database;

class EventsSubscriptions
{
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
        if (static::checkSubscription($memberId, $memberId)) {
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
        if (!static::checkSubscription($eventId, $memberId)) {
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
     * Return false if the member is already subscribed, true otherwise
     *
     * @param int $eventId
     * @param int $memberId
     *
     * @return boolean
     */
    public static function checkSubscription($eventId, $memberId)
    {
        $subscription = Database::getInstance()->prepare(
            "SELECT id FROM tl_calendar_events_subscriptions WHERE pid=? AND member=?"
        )
            ->limit(1)
            ->execute($eventId, $memberId);

        return $subscription->numRows ? false : true;
    }
}
