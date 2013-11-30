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
$GLOBALS['TL_LANG']['tl_calendar']['subscription_reminders'] = array('Enable reminders', 'Enable the e-mail reminders for events in the calendar.');
$GLOBALS['TL_LANG']['tl_calendar']['subscription_time']      = array('Sending time', 'Please enter the approximate time when reminders should be send.');
$GLOBALS['TL_LANG']['tl_calendar']['subscription_days']      = array('Days before event', 'Please enter the comma separated days before event when the reminders should be send.');
$GLOBALS['TL_LANG']['tl_calendar']['subscription_subject']   = array('E-mail subject', 'Please enter the e-mail subject. You can enter wildcards to insert dynamic data (e.g. ##event.title## to display event\'s title)');
$GLOBALS['TL_LANG']['tl_calendar']['subscription_message']   = array('E-mail text', 'Please enter the e-mail text. You can enter wildcards to insert dynamic data (e.g. ##member.firstname## to display member\'s first name).');


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscription_legend'] = 'Subscription settings';
