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
    'Anmeldeeinstellungen überschreiben',
    'Überschreiben Sie die Anmeldeeinstellungen des Kalenders.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_types']              = [
    'Erlaubte Anmelde-Typen',
    'Hier können Sie die erlaubten Anmelde-Typen auswählen.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_maximum']            = [
    'Maximale Anzahl der Anmeldungen',
    'Hier können Sie die maximale Anzahl der Anmeldungen pro Event angeben. 0 steht für unbegrenzte Anmeldungen.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_subscribeEndTime']   = [
    'Anmeldeschluss',
    'Hier können Sie den Zeitversatz vor Beginn des Events festlegen bis wann eine Anmeldung erfolgen kann. Lassen Sie das Feld leer, wenn Sie keinen Anmeldeschluss benötigen.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_unsubscribeEndTime'] = [
    'Abmeldefrist',
    'Hier können Sie den Zeitversatz vor Beginn des Events festlegen ab wann eine Abmeldung nicht mehr möglich ist. Lassen Sie das Feld leer, wenn Sie dies nicht benötigen.'
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_numberOfParticipants'] = ['Enable number of participants', 'Erlaube die Angabe einer Anzahl an Teilnehmern bei der Anmeldung'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_waitingList']        = [
    'Warteliste aktivieren',
    'Erlauben Sie die Anmeldung auf die Warteliste.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_waitingListLimit']   = [
    'Warteliste begrenzen',
    'Bitte geben Sie das Limit der Warteliste an. 0 für keine Begrenzung.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_memberGroupsLimit']   = [
    'Beschränkung auf Mitgliedergruppen',
    'Beschränken Sie die Anmeldung auf bestimmte Mitgliedergruppen. Dies funktioniert nur, wenn Sie den Anmelde-Typ für Mitglieder aktiviert haben!',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_memberGroups']   = [
    'Mitgliedergruppen',
    'Bitte wählen Sie eine oder mehrere Mitgliedergruppen, welche sich anmelden dürfen.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_lastNotificationSent'] = ['Letzte Benachrichtigung gesendet'];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_legend'] = ' Anmeldung Einstellungen';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscriptions'] = [
    'Anmeldungen',
    'Zeigt die Anmeldungen zu Event ID %s.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['sendNotifications'] = ['Event-Benachrichtigungen senden', 'Benachrichtigungen über Event ID %s senden'];

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_timeRef'] = [
    'seconds' => 'Sekunde(n)',
    'minutes' => 'Minute(n)',
    'hours'   => 'Stunde(n)',
    'days'    => 'Tag(e)',
    'weeks'   => 'Woche(n)',
    'months'  => 'Monat(e)',
    'years'   => 'Jahr(e)',
];
