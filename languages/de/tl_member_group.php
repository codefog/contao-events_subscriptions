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
$GLOBALS['TL_LANG']['tl_member_group']['subscription_enableLimit'] = [
    'Anmeldelimit',
    'Begrenzt die Möglichkeit sich für Termine anzumelnden.',
];
$GLOBALS['TL_LANG']['tl_member_group']['subscription_totalLimit']  = [
    'Limit',
    'Für wie viele Termine kann kann man sich anmelden? Für eine ungerenzte Anzahl bitte 0 eintragen.',
];
$GLOBALS['TL_LANG']['tl_member_group']['subscription_periodLimit'] = [
    'Zeitliche Begrenzung',
    'Zusätzlich kann eine Zeitlich Begrenzung gesetzt werden. Als Beispiel 5 Anmeldungen pro Monat.',
];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_member_group']['events_subscription_legend'] = 'Einstellungen der Anmeldung zu Terminen';

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_member_group']['subscription_periodRef'] = [
    'day'  => 'Tag',
    'week' => 'Woche',
    'year' => 'Jahr',
];
