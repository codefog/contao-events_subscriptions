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
$GLOBALS['TL_LANG']['tl_calendar']['subscription_enable']       = [
    'Anmeldung aktivieren',
    'Besuchern erlauben sich für Events dieses Kalenders anzumelden.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_reminders']    = [
    'Erinnerungen aktivieren',
    'Aktiviere Erinnerungen per E-Mail für Events dieses Kalenders.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_time']         = [
    'Sendezeit',
    'Bitte geben Sie die ungefähre Zeit ein, wann Erinnerungen gesendet werden sollen.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_days']         = [
    'Tage vor dem Event',
    'Bitte geben Sie in einer Komma getrennten Liste an, wieviele Tage vor dem Event Erinnerungsmails verschickt werden sollen.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_notification'] = [
    'Benachrichtigung für Erinnerungen',
    'Bitte wählen Sie die Benachrichtigung, welche die Erinnerung versenden soll.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_skipWaitingListReminders'] = [
    'Erinnerungen für Anmeldungen auf der Wartelise überspringen',
    'Sende keine Erinnerungen für Besucher, welche zur Zeit auf der Warteliste stehen.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_unsubscribeLinkPage'] = [
    'Link zur Seite mit der Abmeldebestätigung',
    'Hier können Sie die Seite wählen, welche angezeigt wird wenn der Besucher sich per eindeutigem Link aus einer erhaltenen E-Mail abmeldet.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_subscribeNotification'] = [
    'Benachrichtigung über Anmeldung',
    'Hier können Sie eine benutzerdefinierte Benachrichtigung für die Anmeldung wählen, die anstelle der Standard-Benachrichtigungen dieses Typs verwendet wird.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_unsubscribeNotification'] = [
    'Benachrichtigung über Abmeldung',
    'Hier können Sie eine benutzerdefinierte Benachrichtigung für die Abmeldung wählen, die anstelle der Standard-Benachrichtigungen dieses Typs verwendet wird.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_listUpdateNotification'] = [
    'Benachrichtigung über Veränderung der Warteliste',
    'Hier können Sie eine benutzerdefinierte Benachrichtigung über Anderungen in der Warteliste wählen, die anstelle der Standard-Benachrichtigungen dieses Typs verwendet wird.',
];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscription_legend'] = 'Anmeldung Einstellungen';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview'] = ['Anmeldungen Übersicht', 'Zeige eine Übersicht der Anmeldungen des Kalenders ID %s'];

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview.headline'] = 'Anmeldeübersicht für Kalender "%s"';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview.empty'] = 'Derzeit gibt es keine Anmeldungen.';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview.waitingList'] = 'Warteliste';
