<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions;

use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Contao\CalendarModel;
use Contao\Database;
use Contao\Date;
use Contao\Model\Collection;
use NotificationCenter\Model\Notification;

class Automator
{
    /**
     * @var NotificationSender
     */
    private $sender;

    /**
     * Automator constructor.
     *
     * @param NotificationSender $sender
     */
    public function __construct(NotificationSender $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Send the reminders
     *
     * @return int
     */
    public function sendReminders()
    {
        if (($calendars = $this->getCalendars()) === null) {
            return 0;
        }

        $remindersSent = 0;

        /** @var CalendarModel $calendar */
        foreach ($calendars as $calendar) {
            $remindersSent += $this->processCalendar($calendar);
        }

        return $remindersSent;
    }

    /**
     * Get the calendars
     *
     * @return Collection|null
     */
    private function getCalendars()
    {
        $now = mktime(date('H'), 0, 0, 1, 1, 1970);

        return CalendarModel::findBy(
            [
                'subscription_enable=1',
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
    private function processCalendar(CalendarModel $calendar)
    {
        if (($notification = Notification::findByPk($calendar->subscription_notification)) === null) {
            return 0;
        }

        $events = $this->getEvents($calendar);

        if (count($events) < 1) {
            return 0;
        }

        $sent = 0;

        foreach ($events as $event) {
            if (($subscription = SubscriptionModel::findByPk($event['subscriptionId'])) === null) {
                continue;
            }

            $this->sender->send($notification, $subscription);

            // Update the database
            $subscription->lastReminder = time();
            $subscription->save();

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
    private function getEvents(CalendarModel $calendar)
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
            $where[] = "((e.startTime BETWEEN ".$date->dayBegin." AND ".$date->dayEnd.") AND ((es.lastReminder = 0) OR (es.lastReminder NOT BETWEEN ".$today->dayBegin." AND ".$today->dayEnd.")))";
        }

        $where = (count($where) ? " AND (".implode(" OR ", $where).")" : "");

        return Database::getInstance()->prepare(
            "SELECT e.*, es.id AS subscriptionId FROM tl_calendar_events_subscription es JOIN tl_calendar_events e ON e.id=es.pid WHERE e.pid=? AND es.disableReminders=''".$where
        )
            ->execute($calendar->id)
            ->fetchAllAssoc();
    }
}
