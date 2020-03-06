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
 * Load the language files
 */
\Contao\System::loadLanguageFile('tl_calendar_events_subscription');

/**
 * Add a child table
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['ctable'][] = 'tl_calendar_events_subscription';

/**
 * Register global callbacks
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'][] = [
    'Codefog\EventsSubscriptions\DataContainer\EventsContainer',
    'extendPalette',
];

/**
 * Add list operations
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['sendNotifications'] = [
    'label'           => &$GLOBALS['TL_LANG']['tl_calendar_events']['sendNotifications'],
    'href'            => 'key=subscriptions_notification',
    'icon'            => 'system/modules/events_subscriptions/assets/send.svg',
    'button_callback' => ['Codefog\EventsSubscriptions\DataContainer\EventsContainer', 'getNotificationsButton'],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['subscriptions'] = [
    'label'           => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscriptions'],
    'href'            => 'table=tl_calendar_events_subscription',
    'icon'            => 'mgroup.gif',
    'button_callback' => ['Codefog\EventsSubscriptions\DataContainer\EventsContainer', 'getSubscriptionsButton'],
];

/**
 * Add palettes
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][]              = 'subscription_override';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][]              = 'subscription_waitingList';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][]              = 'subscription_memberGroupsLimit';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['subscription_override']    = 'subscription_types,subscription_memberGroupsLimit,subscription_maximum,subscription_subscribeEndTime,subscription_unsubscribeEndTime,subscription_waitingList';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['subscription_waitingList'] = 'subscription_waitingListLimit';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['subscription_memberGroupsLimit'] = 'subscription_memberGroups';

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_override'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_override'],
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_types'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_types'],
    'exclude'          => true,
    'filter'           => true,
    'inputType'        => 'checkbox',
    'options_callback' => ['Codefog\EventsSubscriptions\DataContainer\EventsContainer', 'getTypes'],
    'reference'        => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['typeRef'],
    'eval'             => ['mandatory' => true, 'multiple' => true, 'tl_class' => 'clr'],
    'sql'              => "blob NULL",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_memberGroupsLimit'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_memberGroupsLimit'],
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_memberGroups'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_memberGroups'],
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkbox',
    'flag' => 1,
    'foreignKey' => 'tl_member_group.name',
    'eval' => ['mandatory' => true, 'multiple' => true, 'tl_class' => 'clr'],
    'sql' => "blob NULL",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_maximum'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_maximum'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => ['rgxp' => 'digit', 'tl_class' => 'clr'],
    'sql'       => "smallint(5) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_subscribeEndTime'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_subscribeEndTime'],
    'exclude'   => true,
    'inputType' => 'timePeriod',
    'options'   => ['seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years'],
    'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_timeRef'],
    'eval'      => ['rgxp' => 'natural', 'minval' => 1, 'tl_class' => 'w50'],
    'sql'       => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_unsubscribeEndTime'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_unsubscribeEndTime'],
    'exclude'   => true,
    'inputType' => 'timePeriod',
    'options'   => ['seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years'],
    'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_timeRef'],
    'eval'      => ['rgxp' => 'natural', 'minval' => 1, 'tl_class' => 'w50'],
    'sql'       => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_waitingList'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_waitingList'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_waitingListLimit'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_waitingListLimit'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => ['rgxp' => 'digit', 'tl_class' => 'clr'],
    'sql'       => "smallint(5) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_lastNotificationSent'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_lastNotificationSent'],
    'exclude'   => true,
    'eval'      => ['rgxp' => 'datim'],
    'sql'       => "varchar(10) NOT NULL default ''",
];
