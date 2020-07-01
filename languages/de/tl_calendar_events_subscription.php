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
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['type']         = [
    'Typ',
    'Bitte wählen Sie den Typ der Anmeldung.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['addedBy'] = [
    'Hinzugefügt von',
    'Hier können Sie wählen, wer diesen Teilnehmer angemeldet hat.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['disableReminders'] = [
    'Erinnerungen deaktivieren',
    'Event Erinnerungen für diesen Teilnehmer nicht aktivieren.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['member'] = [
    'Mitglied',
    'Bitte wählen Sie das Mitglied, das Sie für das Event anmelden möchten.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['firstname'] = ['Vorname', 'Bitte tragen Sie den Vornamen ein.'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['lastname'] = ['Nachname', 'Bitte tragen Sie den Nachnamen ein.'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['email'] = [
    'E-Mail-Adresse',
    'Bitte tragen Sie die E-Mail-Adresse ein.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['dateCreated'] = ['Erstellt am'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['lastReminder'] = ['Letzte Erinnerung am'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['unsubscribeToken'] = ['Abmelde-Token'];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['type_legend'] = 'Anmeldung Einstellungen';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['guest_legend'] = 'Gast Details';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['member_legend'] = 'Mitglied Details';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['new'] = [
    'Neue Anmeldung',
    'Erstellen Sie eine neue Anmeldung.'
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['show'] = [
    'Anmeldung Details',
    'Zeige Details der Anmeldung ID %s',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['edit'] = [
    'Anmeldung bearbeiten',
    'Anmeldung ID %s bearbeiten',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['delete'] = [
    'Anmeldung löschen',
    'Anmeldung ID %s löschen'
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export'] = ['Export', 'Exportieren Sie die Anmeldungen.'];

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['typeRef'] = [
    'guest' => 'Gast',
    'member' => 'Mitglied',
];

/**
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['summary'] = 'Es gibt %s Anmeldungen für dieses Event.';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['summaryMax'] = 'Es gibt %s von %s möglichen Anmeldungen zu diesem Event.';

/**
 * Export
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export.headline'] = 'Anmeldungen exportieren';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export.explanation'] = 'Sie sind dabei, die Daten dieses Events zu exportieren:';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export.count'] = 'Anzahl der zu exportierenden Events:';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export.excelFormatHint'] = 'Um im Excelformat zu exportieren installieren Sie bitte das <strong>phpoffice/phpspreadsheet</strong> Paket. Altrnativ können Sie das veraltete <strong>phpoffice/phpexcel</strong> Paket installieren.';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export.csv'] = 'Export als CSV';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export.excel'] = 'Export als Excel';

/**
 * Notification
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.headline'] = 'Event Benachrichtigungen';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.explanation'] = 'Hier können Sie eine Benachrichtigung zu diesem Event an ausgewählte Mitgliedergruppen senden:';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.notification'] = 'Benachrichtigung';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.memberGroups'] = 'Mitgliedergruppen';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.submit'] = 'Sende eine Benachrichtigung';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.noRecipients'] = 'Es gibt keine aktiven Mitglieder, an welche diese Benachrichtigung gesendet werden kann.';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.confirmation'] = '%s Benachrichtigungen erfolgreich gesendet!';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.lastNotificationDate'] = 'Letzte Benachrichtigung gesendet am/um %s.';
