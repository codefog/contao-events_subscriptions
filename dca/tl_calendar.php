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
 * Load tl_calendar_events data container
 */
\Contao\Controller::loadDataContainer('tl_calendar_events');
\Contao\System::loadLanguageFile('tl_calendar_events');

/**
 * Add list operations
 */
$GLOBALS['TL_DCA']['tl_calendar']['list']['operations']['subscriptions_overview'] = [
    'label'           => &$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview'],
    'href'            => 'key=subscriptions_overview',
    'icon'            => 'mgroup.gif',
    'button_callback' => ['Codefog\EventsSubscriptions\DataContainer\CalendarContainer', 'getSubscriptionsOverviewButton'],
];

/**
 * Add palettes
 */
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_enable';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_waitingList';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_memberGroupsLimit';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_reminders';

\Haste\Dca\PaletteManipulator::create()
    ->addLegend('subscription_legend', 'title_legend', \Haste\Dca\PaletteManipulator::POSITION_AFTER, true)
    ->addField('subscription_enable', 'subscription_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar');

$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_enable']      = 'subscription_types,subscription_memberGroupsLimit,subscription_maximum,subscription_subscribeEndTime,subscription_unsubscribeEndTime,subscription_numberOfParticipants,subscription_waitingList,subscription_reminders,subscription_unsubscribeLinkPage,subscription_subscribeNotification,subscription_unsubscribeNotification,subscription_listUpdateNotification';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_waitingList'] = 'subscription_waitingListLimit';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_memberGroupsLimit'] = 'subscription_memberGroups';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_reminders']   = 'subscription_time,subscription_days,subscription_notification,subscription_skipWaitingListReminders';

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_enable'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_enable'],
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_types']              = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_types'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_maximum']            = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_maximum'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_subscribeEndTime']   = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_subscribeEndTime'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_unsubscribeEndTime'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_unsubscribeEndTime'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_numberOfParticipants'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_numberOfParticipants'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_waitingList']        = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_waitingList'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_waitingListLimit']   = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_waitingListLimit'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_memberGroupsLimit']  = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_memberGroupsLimit'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_memberGroups']       = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_memberGroups'];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_reminders'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_reminders'],
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_time'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_time'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => ['mandatory' => true, 'rgxp' => 'time', 'tl_class' => 'w50'],
    'sql'       => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_days'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_days'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => ['mandatory' => true, 'maxlength' => 32, 'tl_class' => 'w50'],
    'sql'       => "varchar(32) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_notification'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_notification'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['Codefog\EventsSubscriptions\DataContainer\CalendarContainer', 'getNotifications'],
    'eval'             => ['mandatory' => true, 'includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql'              => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_subscribeNotification'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_subscribeNotification'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['Codefog\EventsSubscriptions\DataContainer\CalendarContainer', 'getSubscribeNotifications'],
    'eval'             => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql'              => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_unsubscribeNotification'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_unsubscribeNotification'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['Codefog\EventsSubscriptions\DataContainer\CalendarContainer', 'getUnsubscribeNotifications'],
    'eval'             => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql'              => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_listUpdateNotification'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_listUpdateNotification'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['Codefog\EventsSubscriptions\DataContainer\CalendarContainer', 'getListUpdateNotifications'],
    'eval'             => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql'              => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_skipWaitingListReminders'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_skipWaitingListReminders'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50 m12'],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_unsubscribeLinkPage'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_unsubscribeLinkPage'],
    'exclude' => true,
    'inputType' => 'pageTree',
    'eval' => ['fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql' => "int(10) unsigned NOT NULL default '0'",
];
