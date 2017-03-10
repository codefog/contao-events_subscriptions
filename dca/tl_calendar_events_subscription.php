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
 * Table tl_calendar_events_subscription
 */
$GLOBALS['TL_DCA']['tl_calendar_events_subscription'] = [

    // Config
    'config'   => [
        'dataContainer'     => 'Table',
        'ptable'            => 'tl_calendar_events',
        'onload_callback'   => [
            ['Codefog\EventsSubscriptions\DataContainer\SubscriptionContainer', 'displaySummary'],
        ],
        'onsubmit_callback' => [
            ['Codefog\EventsSubscriptions\DataContainer\SubscriptionContainer', 'dispatchSubscribeEvent'],
        ],
        'ondelete_callback' => [
            ['Codefog\EventsSubscriptions\DataContainer\SubscriptionContainer', 'dispatchUnsubscribeEvent'],
        ],
        'sql'               => [
            'keys' => [
                'id'     => 'primary',
                'pid'    => 'index',
                'member' => 'index',
            ],
        ],
    ],

    // List
    'list'     => [
        'sorting'           => [
            'mode'                  => 4,
            'disableGrouping'       => true,
            'headerFields'          => ['title', 'startDate', 'startTime', 'endDate', 'endTime', 'published'],
            'child_record_callback' => [
                'Codefog\EventsSubscriptions\DataContainer\SubscriptionContainer',
                'listMembers',
            ],
        ],
        'global_operations' => [
            'export' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export'],
                'href'       => 'key=subscriptions_export',
                'icon'       => 'system/modules/events_subscriptions/assets/export.png',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'all'    => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{member_legend},member,addedBy',
    ],

    // Fields
    'fields'   => [
        'id'           => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'pid'          => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp'       => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'member'       => [
            'label'            => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['member'],
            'exclude'          => true,
            'inputType'        => 'select',
            'foreignKey'       => 'tl_member.username',
            'options_callback' => [
                'Codefog\EventsSubscriptions\DataContainer\SubscriptionContainer',
                'getMembers',
            ],
            'eval'             => [
                'mandatory'          => true,
                'includeBlankOption' => true,
                'chosen'             => true,
                'tl_class'           => 'w50',
            ],
            'save_callback'    => [
                ['Codefog\EventsSubscriptions\DataContainer\SubscriptionContainer', 'checkIfAlreadyExists'],
            ],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        'addedBy'      => [
            'label'      => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['addedBy'],
            'default'    => \Contao\BackendUser::getInstance()->id,
            'exclude'    => true,
            'inputType'  => 'select',
            'foreignKey' => 'tl_user.name',
            'eval'       => ['includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'        => "int(10) unsigned NOT NULL default '0'",
        ],
        'lastReminder' => [
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['lastReminder'],
            'flag'  => 8,
            'sql'   => "int(10) unsigned NOT NULL default '0'",
        ],
    ],
];
