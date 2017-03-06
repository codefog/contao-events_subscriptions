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
 * Load tl_member_group data container
 */
\Contao\Controller::loadDataContainer('tl_member_group');
\Contao\System::loadLanguageFile('tl_member_group');

/**
 * Extend palettes
 */
$GLOBALS['TL_DCA']['tl_member']['palettes']['__selector__'][] = 'subscription_enableLimit';

$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace(
    'groups;',
    'groups;{events_subscription_legend},subscription_enableLimit;',
    $GLOBALS['TL_DCA']['tl_member']['palettes']['default']
);

$GLOBALS['TL_DCA']['tl_member']['subpalettes']['subscription_enableLimit'] = 'subscription_totalLimit,subscription_periodLimit';

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_member']['fields']['subscription_enableLimit']          = &$GLOBALS['TL_DCA']['tl_member_group']['fields']['subscription_enableLimit'];
$GLOBALS['TL_DCA']['tl_member']['fields']['subscription_enableLimit']['label'] = &$GLOBALS['TL_LANG']['tl_member']['subscription_enableLimit'];

$GLOBALS['TL_DCA']['tl_member']['fields']['subscription_totalLimit']  = &$GLOBALS['TL_DCA']['tl_member_group']['fields']['subscription_totalLimit'];
$GLOBALS['TL_DCA']['tl_member']['fields']['subscription_periodLimit'] = &$GLOBALS['TL_DCA']['tl_member_group']['fields']['subscription_periodLimit'];
