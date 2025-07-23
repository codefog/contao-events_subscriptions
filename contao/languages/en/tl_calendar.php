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
$GLOBALS['TL_LANG']['tl_calendar']['subscription_subscribeNotification'] = [
    'Subscribe notification',
    'Here you can choose a custom subscribe notification that will be used instead of all notifications of this type.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_unsubscribeNotification'] = [
    'Unsubscribe notification',
    'Here you can choose a custom unsubscribe notification that will be used instead of all notifications of this type.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_listUpdateNotification'] = [
    'Waiting list promotion notification',
    'Here you can choose a custom waiting list promotion notification that will be used instead of all notifications of this type.',
];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscription_legend'] = 'Subscription settings';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview'] = ['Subscriptions overview', 'View an overview of subscriptions of calendar ID %s'];
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_export'] = ['Subscriptions export', 'Export subscriptions of the current calendar'];

/**
 * Export
 */
$GLOBALS['TL_LANG']['tl_calendar']['export.headline'] = 'Export subscriptions';
$GLOBALS['TL_LANG']['tl_calendar']['export.explanation'] = 'You are about to export the subscriptions data for the calendar:';
$GLOBALS['TL_LANG']['tl_calendar']['export.explanationFilters'] = 'Use the fields below to limit the exported subscriptions by date bounds. The filter will affect the date of subscription.';
$GLOBALS['TL_LANG']['tl_calendar']['export.startDate'] = ['Start date', 'Here you can enter the subscriptions start date.'];
$GLOBALS['TL_LANG']['tl_calendar']['export.endDate'] = ['End date', 'Here you can enter the subscriptionsm end date.'];
$GLOBALS['TL_LANG']['tl_calendar']['export.excelFormatHint'] = 'To enable export in Excel format please install the <strong>phpoffice/phpspreadsheet</strong> package. Alternatively you can install the deprecated <strong>phpoffice/phpexcel</strong> package.';
$GLOBALS['TL_LANG']['tl_calendar']['export.csv'] = 'Export as CSV';
$GLOBALS['TL_LANG']['tl_calendar']['export.excel'] = 'Export as Excel';

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview.headline'] = 'Subscriptions overview for calendar "%s"';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview.empty'] = 'Currently there are no subscriptions.';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview.waitingList'] = 'Waiting list';
