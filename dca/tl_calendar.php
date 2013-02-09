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
 * Add palettes to tl_calendar
 */
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_reminders';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] = str_replace('jumpTo;', 'jumpTo;{subscription_legend:hide},subscription_reminders;', $GLOBALS['TL_DCA']['tl_calendar']['palettes']['default']);
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_reminders'] = 'subscription_time,subscription_days,subscription_subject,subscription_message';


/**
 * Add fields to tl_calendar
 */
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_reminders'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_reminders'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_time'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_time'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'rgxp'=>'time', 'tl_class'=>'w50'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_days'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_days'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>32, 'tl_class'=>'w50'),
	'sql'                     => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_subject'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_subject'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'decodeEntities'=>true, 'tl_class'=>'long clr'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_message'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['subscription_message'],
	'exclude'                 => true,
	'inputType'               => 'textarea',
	'eval'                    => array('mandatory'=>true, 'decodeEntities'=>true),
	'sql'                     => "text NULL"
);
