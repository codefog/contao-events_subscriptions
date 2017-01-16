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
 * Add a child table to tl_calendar_events
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['ctable'][] = 'tl_calendar_events_subscriptions';

/**
 * Register global callbacks
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'][] = [
    'Codefog\EventsSubscriptions\DataContainer\EventsContainer',
    'extendPalette',
];

/**
 * Add a new button to tl_calendar_events
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['subscriptions'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscriptions'],
    'href'  => 'table=tl_calendar_events_subscriptions',
    'icon'  => 'mgroup.gif',
];

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_maximum'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_maximum'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => ['rgxp' => 'digit', 'tl_class' => 'w50'],
    'sql'       => "smallint(5) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_lastDay'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_lastDay'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => ['datepicker' => true, 'rgxp' => 'datim', 'tl_class' => 'w50 wizard'],
    'sql'       => "varchar(10) NOT NULL default ''",
];
