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
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribe']               = 'Anmelden';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribe']             = 'Abmelden';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeConfirmation']   = 'Sie haben sich für den Termin angemeldet.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeConfirmation'] = 'Sie haben sich vom Termin abgemeldet.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeNotAllowed']     = 'Es ist nicht mehr möglich sich zu diesem Termin anzumelden.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeNotAllowed']   = 'Es ist nicht mehr möglich sich zu diesem Termin abzumelden.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.onWaitingList']           = 'Warteliste';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.enableReminders']         = 'Erinnerung aktivieren';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.firstname']     = 'Vorname';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.lastname']      = 'Nachname';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.email']         = 'E-Mailadresse';

/**
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['events_subscriptions.memberAlreadySubscribed'] = 'Teilnehmer ID %s ist für den Termin bereits angemeldet!';

/**
 * Export
 */
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.exportHeaderFields'] = [
    'event_id'                 => 'Termin ID',
    'event_title'              => 'Termin Titel',
    'event_start'              => 'Startdatum',
    'event_end'                => 'Enddatum',
    'subscription_type'        => 'Typ',
    'subscription_waitingList' => 'Warteliste',
    'subscription_firstname'   => 'Vorname',
    'subscription_lastname'    => 'Nachname',
    'subscription_email'       => 'E-Mailadresse',
];

$GLOBALS['TL_LANG']['MSC']['events_subscriptions.memberExportHeaderFields'] = [
    'member_id'       => 'Benutzer ID',
    'member_username' => 'Benutzername',
];
