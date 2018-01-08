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
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_override']           = [
    'Einstellungen der Anmeldung überschreiben',
    'Überschreibe die Anmeldeeinstellungen des Kalenders.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_types']              = [
    'Erlaubte Typen',
    'Welche Gruppen sollen sich anmelden können?',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_maximum']            = [
    'Maximale Teilnehmerzahl',
    '0 für keine Begrenzung.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_subscribeEndTime']   = [
    'Zeit bis zum Ende der Anmeldung',
    'Wenn die Anmeldung bis zum Start des Termines möglich sein soll, muss das Feld leer sein.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_unsubscribeEndTime'] = [
    'Zeit bis zum Ende der Abmeldung',
    'Wenn die Abmeldung bis zum Start des Termines möglich sein soll, muss das Feld leer sein.'
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_waitingList']        = [
    'Warteliste aktivieren',
    'Benutzern erlauben sich auf die Warteliste zu schreiben.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_waitingListLimit']   = [
    'Wartelistenlimit',
    '0 für keine Begrenzung.',
];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_legend'] = 'Einstellungen der Anmeldung';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscriptions'] = [
    'Angemeldete Teilnehmer',
    'Zeigt die Teilnehmer, die sich für den Termin ID %s angemeldet haben.',
];

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_timeRef'] = [
    'seconds' => 'Sekunde',
    'minutes' => 'Minute',
    'hours'   => 'Stunde',
    'days'    => 'Tag',
    'weeks'   => 'Woche',
    'months'  => 'Monat',
    'years'   => 'Jahr',
];
