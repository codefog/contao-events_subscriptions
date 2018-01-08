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
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['type'] = ['Typ', '',];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['addedBy'] = [
    'Eingetragen von',
    'Hier können Sie wählen, wer diesen Teilnehmer eingetragen hat.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['member'] = [
    'Teilnehmer',
    'Bitte wählen Sie den Teilnehmer, de Sie für den Termin eintragen möchten.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['firstname'] = ['Vorname', ''];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['lastname'] = ['Nachname', ''];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['email'] = [
    'E-Mailadresse',
    '',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['dateCreated'] = ['Erstellt am'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['lastReminder'] = ['Letzte Erinnerung'];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['type_legend'] = 'Einstellungen der Anmeldung';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['guest_legend'] = 'Gast Details';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['member_legend'] = 'Benutzer Details';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['new'] = ['Neue Anmeldung', 'Neue Anmeldung eintragen'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['show'] = [
    'Details',
    'Zeigt Details der Anmeldung ID %s',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['edit'] = [
    'Anmeldung bearbeiten',
    'Anmeldung ID %s bearbeiten',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['delete'] = ['Anmeldung löschen', 'Löscht die Anmeldung ID %s'];


$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export'] = ['Export', ''];

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['typeRef'] = [
    'guest' => 'Gast',
    'member' => 'Benutzer',
];

/**
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['summary'] = 'Aktuell haben sich %s Personen zu diesem Termin angemeldet.';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['summaryMax'] = 'Aktuell haben sich %s Personen von maximal %s Personen zu diesem Termin angemeldet.';
