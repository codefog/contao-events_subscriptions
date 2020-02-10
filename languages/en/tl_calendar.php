<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscription_enable']       = [
    'Enable the subscriptions',
    'Allow the users to subscribe to the events of this calendar.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_reminders']    = [
    'Enable reminders',
    'Enable the e-mail reminders for events in the calendar.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_time']         = [
    'Sending time',
    'Please enter the approximate time when reminders should be send.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_days']         = [
    'Days before event',
    'Please enter the comma separated days before event when the reminders should be send.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_notification'] = [
    'Reminder notification',
    'Please choose the notification that will be used to sent the reminder.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_skipWaitingListReminders'] = [
    'Skip reminders for waiting list subscribers',
    'Do not send reminders for subscribers that are currently on the waiting list.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_unsubscribeLinkPage'] = [
    'Unsubscribe link confirmation page',
    'Here you can choose the page that will be displayed after the user unsubscribes via unique link he receives in e-mail.',
];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscription_legend'] = 'Subscription settings';
