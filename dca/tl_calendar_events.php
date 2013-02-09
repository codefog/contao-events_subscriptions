<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 * 
 * Copyright (C) 2013 Codefog
 * 
 * @package events_subscriptions
 * @link    http://codefog.pl
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */


/**
 * Add a child table to tl_calendar_events
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['ctable'][] = 'tl_calendar_events_subscriptions';


/**
 * Add a new button to tl_calendar_events
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['subscriptions'] = array
(
	'label'               => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscriptions'],
	'href'                => 'table=tl_calendar_events_subscriptions',
	'icon'                => 'mgroup.gif'
);
