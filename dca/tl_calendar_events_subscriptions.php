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
 * Table tl_calendar_events_subscriptions
 */
$GLOBALS['TL_DCA']['tl_calendar_events_subscriptions'] = array
(

    // Config
    'config'   => array
    (
        'dataContainer'   => 'Table',
        'ptable'          => 'tl_calendar_events',
        'onload_callback' => [
            ['Codefog\EventsSubscriptions\DataContainer\SubscriptionsContainer', 'displaySummary'],
        ],
        'sql'             => array
        (
            'keys' => array
            (
                'id'     => 'primary',
                'pid'    => 'index',
                'member' => 'index',
            ),
        ),
    ),

    // List
    'list'     => array
    (
        'sorting'           => array
        (
            'mode'                  => 4,
            'disableGrouping'       => true,
            'headerFields'          => array('title', 'startDate', 'startTime', 'endDate', 'endTime', 'published'),
            'child_record_callback' => array(
                'Codefog\EventsSubscriptions\DataContainer\SubscriptionsContainer',
                'listMembers',
            ),
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ),
        ),
        'operations'        => array
        (
            'edit'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscriptions']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ),
            'delete' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_calendar_events_subscriptions']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
            ),
            'show'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscriptions']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ),
        ),
    ),

    // Palettes
    'palettes' => array
    (
        'default' => '{member_legend},member,addedBy',
    ),

    // Fields
    'fields'   => array
    (
        'id'        => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'pid'       => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'tstamp'    => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'member'    => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_calendar_events_subscriptions']['member'],
            'exclude'          => true,
            'inputType'        => 'select',
            'foreignKey'       => 'tl_member.username',
            'options_callback' => array(
                'Codefog\EventsSubscriptions\DataContainer\SubscriptionsContainer',
                'getMembers',
            ),
            'eval'             => array(
                'mandatory'          => true,
                'includeBlankOption' => true,
                'chosen'             => true,
                'tl_class'           => 'w50',
            ),
            'save_callback'    => array
            (
                array('Codefog\EventsSubscriptions\DataContainer\SubscriptionsContainer', 'checkIfAlreadyExists'),
            ),
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ),
        'addedBy'   => array
        (
            'label'      => &$GLOBALS['TL_LANG']['tl_calendar_events_subscriptions']['addedBy'],
            'default'    => \Contao\BackendUser::getInstance()->id,
            'exclude'    => true,
            'inputType'  => 'select',
            'foreignKey' => 'tl_user.name',
            'eval'       => array('includeBlankOption' => true, 'tl_class' => 'w50'),
            'sql'        => "int(10) unsigned NOT NULL default '0'",
        ),
        'lastEmail' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscriptions']['lastEmail'],
            'flag'  => 8,
            'sql'   => "int(10) unsigned NOT NULL default '0'",
        ),
    ),
);
