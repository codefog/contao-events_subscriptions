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
 * Load tl_calendar_events data container
 */
\Contao\Controller::loadDataContainer('tl_calendar_events');
\Contao\System::loadLanguageFile('tl_calendar_events');

/**
 * Add palettes
 */
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_enable';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_reminders';

$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] = str_replace(
    'jumpTo;',
    'jumpTo;{subscription_legend:hide},subscription_enable,subscription_reminders;',
    $GLOBALS['TL_DCA']['tl_calendar']['palettes']['default']
);

$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_enable']    = 'subscription_maximum,subscription_lastDay';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_reminders'] = 'subscription_time,subscription_days,subscription_notification';

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

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_maximum'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_maximum'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_lastDay'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_lastDay'];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_reminders'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_reminders'],
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true, 'tl_class'=> 'clr'],
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
