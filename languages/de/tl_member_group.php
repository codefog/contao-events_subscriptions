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
    'Anmeldelimit aktivieren',
    'Begrenzt die Zahl der Anmeldungen für Mitglieder aus dieser Gruppe.',
];
$GLOBALS['TL_LANG']['tl_member_group']['subscription_totalLimit']  = [
    'Gesamtlimit',
    'Hier können Sie das gesamte Limit der Anmeldungen angeben. 0 für eine unbegrenzte Anzahl.',
];
$GLOBALS['TL_LANG']['tl_member_group']['subscription_periodLimit'] = [
    'Zeitraum des Limits',
    'Hier können Sie die Limitierung der Anmeldungen zusätzlich auf einen Zeitraum festlegen. Zum Beispiel können Sie nur 5 Anmeldungen pro Monat zulassen.',
  ];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_member_group']['events_subscription_legend'] = 'Einstellungen Event-Anmeldungen';

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_member_group']['subscription_periodRef'] = [
    'day'  => 'Tag',
    'week' => 'Woche',
    'year' => 'Jahr',
];
