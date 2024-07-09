<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/**
 * Load tl_member_group data container
 */
\Contao\Controller::loadDataContainer('tl_member_group');
\Contao\System::loadLanguageFile('tl_member_group');

/**
 * Global config
 */
$GLOBALS['TL_DCA']['tl_member']['config']['ondelete_callback'][] = [\Codefog\EventsSubscriptions\EventListener\MemberListener::class, 'onDeleteCallback'];

/**
 * Extend palettes
 */
$GLOBALS['TL_DCA']['tl_member']['palettes']['__selector__'][] = 'subscription_enableLimit';

PaletteManipulator::create()
    ->addLegend('events_subscription_legend', 'groups_legend', PaletteManipulator::POSITION_AFTER, true)
    ->addField('subscription_enableLimit', 'events_subscription_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_member');

$GLOBALS['TL_DCA']['tl_member']['subpalettes']['subscription_enableLimit'] = 'subscription_totalLimit,subscription_periodLimit';

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_member']['fields']['subscription_enableLimit']          = &$GLOBALS['TL_DCA']['tl_member_group']['fields']['subscription_enableLimit'];
$GLOBALS['TL_DCA']['tl_member']['fields']['subscription_enableLimit']['label'] = &$GLOBALS['TL_LANG']['tl_member']['subscription_enableLimit'];

$GLOBALS['TL_DCA']['tl_member']['fields']['subscription_totalLimit']  = &$GLOBALS['TL_DCA']['tl_member_group']['fields']['subscription_totalLimit'];
$GLOBALS['TL_DCA']['tl_member']['fields']['subscription_periodLimit'] = &$GLOBALS['TL_DCA']['tl_member_group']['fields']['subscription_periodLimit'];
