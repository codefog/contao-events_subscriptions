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
 * Load tl_member language file
 */
\Contao\System::loadLanguageFile('tl_member');

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_member']['subscription_enableLimit'] = [
    'Enable subscription limit',
    'Limit the subscriptions for the member. This will override the member group settings!',
];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_member']['events_subscription_legend'] = &$GLOBALS['TL_LANG']['tl_member_group']['events_subscription_legend'];
