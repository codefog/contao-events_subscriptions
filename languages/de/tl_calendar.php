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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscription_reminders'] = array('Erinnerungmails aktivieren', 'Aktiviert Erinnerungs-E-Mails für im Kalender eingetragene Anlässe.');
$GLOBALS['TL_LANG']['tl_calendar']['subscription_time']      = array('Sendezeit', 'Bitte geben Sie die ungefähre Zeit ein, wann die Erinnerung verschickt werden sollte.');
$GLOBALS['TL_LANG']['tl_calendar']['subscription_days']      = array('Tage vor dem Anlass', 'Bitte geben Sie in einer Komma getrennten Liste an, wieviele Tage vor dem Anlass Erinnerungsmails verschickt werden sollten.');
$GLOBALS['TL_LANG']['tl_calendar']['subscription_subject']   = array('E-Mail-Betreff', 'Bitte geben Sie den E-Mail-Betreff an. Sie können Wildcards für dynamisches Einfügen von Inhalten verwenden (z.B. ##event.title## für den Titel des Anlasses).');
$GLOBALS['TL_LANG']['tl_calendar']['subscription_message']   = array('E-Mail Inhalt', 'Bitte geben Sie den Inhaltstext der Mail-Nachricht an. Sie können Wildcards für das dynamische Einfügen von Inhalten verwenden (z.B. ##member.firstname## für den Vornamen eines Teilnehmers, einer Teilnehmerin).');


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscription_legend'] = 'Anmelde-Einstellungen';
