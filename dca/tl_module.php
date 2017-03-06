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
 * Add palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['event_list_subscribe'] = '{title_legend},name,headline,type;{config_legend},cal_calendar,cal_order,cal_limit,perPage;{redirect_legend:hide},jumpTo_subscribe,jumpTo_unsubscribe,jumpTo_login;{template_legend:hide},cal_template,imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['event_subscribe']      = '{title_legend},name,headline,type;{redirect_legend:hide},jumpTo_subscribe,jumpTo_unsubscribe,jumpTo_login;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['event_subscriptions']  = '{title_legend},name,headline,type;{config_legend},cal_calendar,cal_noSpan,cal_format,cal_ignoreDynamic,cal_order,cal_readerModule,cal_limit,perPage;{template_legend:hide},cal_template,customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo_subscribe'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['jumpTo_subscribe'],
    'exclude'   => true,
    'inputType' => 'pageTree',
    'eval'      => ['fieldType' => 'radio'],
    'sql'       => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo_unsubscribe'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['jumpTo_unsubscribe'],
    'exclude'   => true,
    'inputType' => 'pageTree',
    'eval'      => ['fieldType' => 'radio'],
    'sql'       => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo_login'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['jumpTo_login'],
    'exclude'   => true,
    'inputType' => 'pageTree',
    'eval'      => ['fieldType' => 'radio'],
    'sql'       => "int(10) unsigned NOT NULL default '0'",
];
