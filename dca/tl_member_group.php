<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * Copyright (C) 2013 Codefog
 *
 * @package events_subscriptions
 * @author  Codefog <http://codefog.pl>
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */

/**
 * Extend palettes
 */
$GLOBALS['TL_DCA']['tl_member_group']['palettes']['__selector__'][] = 'subscription_enableLimit';

$GLOBALS['TL_DCA']['tl_member_group']['palettes']['default'] = str_replace(
    'redirect;',
    'redirect;{events_subscription_legend},subscription_enableLimit;',
    $GLOBALS['TL_DCA']['tl_member_group']['palettes']['default']
);

$GLOBALS['TL_DCA']['tl_member_group']['subpalettes']['subscription_enableLimit'] = 'subscription_totalLimit,subscription_periodLimit';

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_member_group']['fields']['subscription_enableLimit'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_member_group']['subscription_enableLimit'],
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_member_group']['fields']['subscription_totalLimit'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_member_group']['subscription_totalLimit'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => ['rgxp' => 'natural', 'tl_class' => 'w50'],
    'sql'       => "smallint(5) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_member_group']['fields']['subscription_periodLimit'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_member_group']['subscription_periodLimit'],
    'exclude'   => true,
    'inputType' => 'timePeriod',
    'options'   => ['day', 'month', 'year'],
    'reference' => &$GLOBALS['TL_DCA']['tl_member_group']['fields']['subscription_periodRef'],
    'eval'      => ['rgxp' => 'natural', 'minval' => 1, 'tl_class' => 'w50'],
    'sql'       => "varchar(64) NOT NULL default ''",
];
