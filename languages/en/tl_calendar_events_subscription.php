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
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['type']         = [
    'Type',
    'Please choose the subscription type.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['addedBy']      = [
    'Added by',
    'Here you can choose who subscribed this member.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['member']       = [
    'Member',
    'Please choose the member you want to subscribe to the event.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['firstname']    = ['First name', 'Please enter the first name.'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['lastname']     = ['Lastname', 'Please enter the last name.'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['email']        = [
    'E-mail address',
    'Please enter the e-mail address.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['dateCreated']  = ['Date created'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['lastReminder'] = ['Last reminder sent'];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['type_legend']   = 'Subscription settings';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['guest_legend']  = 'Guest details';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['member_legend'] = 'Member details';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['new']    = [
    'New subscription',
    'Create a new subscriptions',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['show']   = [
    'Subscription details',
    'Show details of subscription ID %s',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['edit']   = [
    'Edit subscription',
    'Edit subscription ID %s',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['delete'] = [
    'Delete subscription',
    'Delete subscription ID %s',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export'] = ['Export', 'Export the subscriptions.'];

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['typeRef'] = [
    'guest'  => 'Guest',
    'member' => 'Member',
];

/**
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['summary']    = 'There are %s subscription(s) to this event.';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['summaryMax'] = 'There are %s subscription(s) of %s possible to this event.';
