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
 * Add palettes to tl_calendar
 */
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_reminders';

$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] = str_replace(
    'jumpTo;',
    'jumpTo;{subscription_legend:hide},subscription_reminders;',
    $GLOBALS['TL_DCA']['tl_calendar']['palettes']['default']
);

$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_reminders'] = 'subscription_time,subscription_days,subscription_notification';

/**
 * Add fields to tl_calendar
 */
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_reminders'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_reminders'],
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true],
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
