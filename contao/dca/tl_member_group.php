<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Doctrine\DBAL\Types\Types;

// Palettes
$GLOBALS['TL_DCA']['tl_member_group']['palettes']['__selector__'][] = 'subscription_enableLimit';

PaletteManipulator::create()
    ->addLegend('events_subscription_legend', 'redirect_legend', PaletteManipulator::POSITION_AFTER, true)
    ->addField('subscription_enableLimit', 'events_subscription_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_member_group');

$GLOBALS['TL_DCA']['tl_member_group']['subpalettes']['subscription_enableLimit'] = 'subscription_totalLimit,subscription_periodLimit';

// Fields
$GLOBALS['TL_DCA']['tl_member_group']['fields']['subscription_enableLimit'] = [
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_member_group']['fields']['subscription_totalLimit'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'natural', 'tl_class' => 'w50'],
    'sql' => ['type' => Types::SMALLINT, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_member_group']['fields']['subscription_periodLimit'] = [
    'inputType' => 'timePeriod',
    'options' => ['day', 'month', 'year'],
    'reference' => &$GLOBALS['TL_LANG']['tl_member_group']['subscription_periodRef'],
    'eval' => ['rgxp' => 'natural', 'minval' => 1, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::STRING, 'length' => 64, 'default' => ''],
];
