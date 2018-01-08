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
 * Load tl_member language file
 */
\Contao\System::loadLanguageFile('tl_member');

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_member']['subscription_enableLimit'] = [
    'Anmeldelimit',
    'Begrenzt die Möglichkeit sich für Termine anzumelnden. Die Gruppeneinstellungen werden überschrieben!',
];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_member']['events_subscription_legend'] = &$GLOBALS['TL_LANG']['tl_member_group']['events_subscription_legend'];
