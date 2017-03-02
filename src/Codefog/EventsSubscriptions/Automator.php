<?php

namespace Codefog\EventsSubscriptions;

use Contao\CalendarModel;
use Contao\Config;
use Contao\Database;
use Contao\Date;
use Contao\MemberModel;
use Contao\Model\Collection;
use Haste\Util\Format;
use NotificationCenter\Model\Notification;

class Automator
{
    /**
     * Send the e-mail reminders
     *
     * @return int
     */
    public static function sendEmailReminders()
    {
        if (($calendars = static::getCalendars()) === null) {
            return 0;
        }

        $remindersSent = 0;

        /** @var CalendarModel $calendar */
        foreach ($calendars as $calendar) {
            $remindersSent += static::processCalendar($calendar);
        }

        return $remindersSent;
    }

    /**
     * Get the calendars
     *
     * @return Collection|null
     */
    private static function getCalendars()
    {
        $now = mktime(date('H'), 0, 0, 1, 1, 1970);

        return CalendarModel::findBy(
            [
                'subscription_reminders=1',
                '(subscription_time >= ?)',
                '(subscription_time <= ?)',
                'subscription_notification>0',
            ],
            [$now, $now + 3600]
        );
    }

    /**
     * Process the calendar
     *
     * @param CalendarModel $calendar
     *
     * @return int
     *
     * @throws \Exception
     */
    private static function processCalendar(CalendarModel $calendar)
    {
        if (($notification = Notification::findByPk($calendar->subscription_notification)) === null) {
            return 0;
        }

        $events = static::getEvents($calendar);

        if (count($events) < 1) {
            return 0;
        }

        $sent = 0;

        foreach ($events as $event) {
            if (($member = MemberModel::findByPk($event['member'])) === null) {
                continue;
            }

            $notification->send(static::generateTokens($calendar, $event, $member), $member->language);

            // Update the database
            Database::getInstance()->prepare(
                "UPDATE tl_calendar_events_subscription SET lastEmail=? WHERE pid=? AND member=?"
            )->execute(time(), $event['id'], $member->id);

            // Bump the counter
            $sent++;
        }

        return $sent;
    }

    /**
     * Get the events
     *
     * @param CalendarModel $calendar
     *
     * @return array
     */
    private static function getEvents(CalendarModel $calendar)
    {
        $days = array_map('intval', trimsplit(',', $calendar->subscription_days));

        if (count($days) < 1) {
            return [];
        }

        $today = new Date();
        $where = [];

        // Bulid a WHERE statement
        foreach ($days as $day) {
            $date    = new Date(strtotime('+'.$day.' days'));
            $where[] = "((e.startTime BETWEEN ".$date->dayBegin." AND ".$date->dayEnd.") AND ((es.lastEmail = 0) OR (es.lastEmail NOT BETWEEN ".$today->dayBegin." AND ".$today->dayEnd.")))";
        }

        $where = (count($where) ? " AND (".implode(" OR ", $where).")" : "");

        return Database::getInstance()->prepare(
            "SELECT e.*, es.member FROM tl_calendar_events_subscription es JOIN tl_calendar_events e ON e.id=es.pid WHERE e.pid=?".$where
        )
            ->execute($calendar->id)
            ->fetchAllAssoc();
    }

    /**
     * Generate the tokens
     *
     * @param CalendarModel $calendar
     * @param array         $event
     * @param MemberModel   $member
     *
     * @return array
     */
    private static function generateTokens(CalendarModel $calendar, array $event, MemberModel $member)
    {
        $tokens = ['admin_email' => $GLOBALS['TL_ADMIN_EMAIL'] ?: Config::get('adminEmail')];

        // Calendar tokens
        foreach ($calendar->row() as $k => $v) {
            $tokens['calendar_'.$k] = Format::dcaValue('tl_calendar', $k, $v);
        }

        // Event tokens
        foreach ($event as $k => $v) {
            $tokens['event_'.$k] = Format::dcaValue('tl_calendar_events', $k, $v);
        }

        // Member tokens
        foreach ($member->row() as $k => $v) {
            $tokens['member_'.$k] = Format::dcaValue('tl_member', $k, $v);
        }

        return $tokens;
    }
}
