<?php

/**
 * FR Translation for events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @author    Web ex Machina <https://www.webexmachina.fr>
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
    'Activer la limite d\'inscription',
    'Limiter les inscriptions pour ce membre. Cela modifiera les paramètres de son groupe.',
];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_member']['events_subscription_legend'] = &$GLOBALS['TL_LANG']['tl_member_group']['events_subscription_legend'];