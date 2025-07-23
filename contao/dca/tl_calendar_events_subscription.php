<?php

use Contao\DC_Table;
use Doctrine\DBAL\Types\Types;

$GLOBALS['TL_DCA']['tl_calendar_events_subscription'] = [

    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_calendar_events',
        'doNotCopyRecords' => true,
        'onload_callback' => [
            ['Codefog\EventsSubscriptionsBundle\DataContainer\SubscriptionContainer', 'addSendNotificationCheckbox'],
            ['Codefog\EventsSubscriptionsBundle\DataContainer\SubscriptionContainer', 'displaySummary'],
        ],
        'onsubmit_callback' => [
            ['Codefog\EventsSubscriptionsBundle\DataContainer\SubscriptionContainer', 'setDateCreated'],
            ['Codefog\EventsSubscriptionsBundle\DataContainer\SubscriptionContainer', 'setUnsubscribeToken'],
            ['Codefog\EventsSubscriptionsBundle\DataContainer\SubscriptionContainer', 'dispatchSubscribeEvent'],
        ],
        'ondelete_callback' => [
            ['Codefog\EventsSubscriptionsBundle\DataContainer\SubscriptionContainer', 'dispatchUnsubscribeEvent'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
                'member' => 'index',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => \Contao\DataContainer::MODE_PARENT,
            'disableGrouping' => true,
            'fields' => ['dateCreated'],
            'headerFields' => ['title', 'startDate', 'startTime', 'endDate', 'endTime', 'published'],
            'panelLayout' => 'filter;limit',
            'child_record_callback' => [
                'Codefog\EventsSubscriptionsBundle\DataContainer\SubscriptionContainer',
                'generateLabel',
            ],
        ],
        'global_operations' => [
            'newFromMemberGroup' => [
                'href' => 'key=subscriptions_newFromMemberGroup',
                'icon' => 'new.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'export' => [
                'href' => 'key=subscriptions_export',
                'icon' => 'theme_import.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'all',
        ],
        'operations' => [
            'edit',
            'delete',
            'show',
        ],
    ],

    // Palettes
    'palettes' => [
        '__selector__' => ['type'],
        'default' => '{type_legend},type,addedBy,numberOfParticipants,disableReminders',
        'guest' => '{type_legend},type,addedBy,numberOfParticipants,disableReminders;{guest_legend},firstname,lastname,email',
        'member' => '{type_legend},type,addedBy,numberOfParticipants,disableReminders;{member_legend},member',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'pid' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
        'tstamp' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
        'dateCreated' => [
            'flag' => \Contao\DataContainer::SORT_MONTH_DESC,
            'eval' => ['doNotCopy' => true, 'rgxp' => 'datim'],
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
        'type' => [
            'filter' => true,
            'inputType' => 'select',
            'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\SubscriptionContainer', 'getTypes'],
            'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['typeRef'],
            'eval' => [
                'mandatory' => true,
                'includeBlankOption' => true,
                'submitOnChange' => true,
                'tl_class' => 'w50',
            ],
            'sql' => ['type' => Types::STRING, 'length' => 32, 'default' => ''],
        ],
        'addedBy' => [
            // TODO
            'default' => \Contao\BackendUser::getInstance()->id,
            'filter' => true,
            'inputType' => 'select',
            'foreignKey' => 'tl_user.name',
            'eval' => ['doNotCopy' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
        'numberOfParticipants' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'natural', 'minval' => 1, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::SMALLINT, 'unsigned' => true, 'default' => 1],
        ],
        'disableReminders' => [
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => ['type' => Types::BOOLEAN, 'default' => false],
        ],
        'sendNotification' => [
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::BOOLEAN, 'default' => true],
        ],
        'member' => [
            'inputType' => 'select',
            'foreignKey' => 'tl_member.username',
            'options_callback' => [
                'Codefog\EventsSubscriptionsBundle\DataContainer\SubscriptionContainer',
                'getMembers',
            ],
            'eval' => [
                'mandatory' => true,
                'includeBlankOption' => true,
                'chosen' => true,
                'doNotCopy' => true,
                'tl_class' => 'w50',
            ],
            'save_callback' => [
                ['Codefog\EventsSubscriptionsBundle\DataContainer\SubscriptionContainer', 'checkIfAlreadyExists'],
            ],
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
        'firstname' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'lastname' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'email' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'email', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'lastReminder' => [
            'flag' => \Contao\DataContainer::SORT_MONTH_DESC,
            'eval' => ['doNotCopy' => true],
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
        'unsubscribeToken' => [
            'eval' => ['doNotCopy' => true],
            'sql' => ['type' => Types::STRING, 'length' => 32, 'default' => ''],
        ],
    ],
];
