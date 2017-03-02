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

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_override']         = [
    'Override subscription settings',
    'Override the subscription settings set in the calendar settings.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_maximum']          = [
    'Maximum number of subscriptions',
    'Here you can set the maximum number of subscriptions. Leave 0 to allow unlimited subscriptions.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_subscribeEndTime'] = [
    'Subscribe end time',
    'Here you can set the time offset before start of the event when the subscription closes. Leave empty to not limit the time to subscribe.',
];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_legend'] = 'Subscription settings';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['members'] = [
    'Subscribed members',
    'Show the subscribed members to event ID %s',
];

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_timeRef'] = [
    'seconds' => 'second(s)',
    'minutes' => 'minute(s)',
    'hours'   => 'hour(s)',
    'days'    => 'day(s)',
    'weeks'   => 'week(s)',
    'months'  => 'month(s)',
    'years'   => 'year(s)',
];
