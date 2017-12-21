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
    'Enable subscription limit',
    'Limit the subscriptions for members in this group.',
];
$GLOBALS['TL_LANG']['tl_member_group']['subscription_totalLimit']  = [
    'Total limit',
    'Here you can enter the subscription total limit. Enter 0 to allow unlimited subscriptions.',
];
$GLOBALS['TL_LANG']['tl_member_group']['subscription_periodLimit'] = [
    'Limit period',
    'Here you can additionally limit the subscriptions by period. For example you can allow only 5 subscriptions per month.',
];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_member_group']['events_subscription_legend'] = 'Events subscriptions settings';

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_member_group']['subscription_periodRef'] = [
    'day'  => 'day',
    'week' => 'week',
    'year' => 'year',
];
