<?php

use Contao\ArrayUtil;
use Doctrine\DBAL\Types\Types;

// Load the language files
\Contao\System::loadLanguageFile('tl_calendar');
\Contao\System::loadLanguageFile('tl_calendar_events_subscription');

// Config
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['ctable'][] = 'tl_calendar_events_subscription';

$GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'][] = [
    'Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer',
    'extendPalette',
];

if (($index = array_search(['tl_calendar_events', 'checkPermission'], $GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'])) !== false) {
    $GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'][$index] = [
        'Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer',
        'checkPermission',
    ];
}

// Global operations
ArrayUtil::arrayInsert($GLOBALS['TL_DCA']['tl_calendar_events']['list']['global_operations'], 0, [
    'subscriptions_overview' => [
        'href' => 'key=subscriptions_overview',
        'icon' => 'mgroup.svg',
        'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer', 'getSubscriptionsOverviewButton'],
    ],
    'subscriptions_export' => [
        'href' => 'key=subscriptions_exportCalendar',
        'icon' => 'system/modules/events_subscriptions/assets/export.png', // TODO
        'attributes' => 'onclick="Backend.getScrollOffset()"',
        'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer', 'getSubscriptionsExportButton'],
    ],
]);

// List operations
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['sendNotifications'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['sendNotifications'],
    'href' => 'key=subscriptions_notification',
    'icon' => 'system/modules/events_subscriptions/assets/send.svg', // TODO
    'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer', 'getNotificationsButton'],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['subscriptions'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscriptions'],
    'href' => 'table=tl_calendar_events_subscription',
    'icon' => 'mgroup.svg',
    'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer', 'getSubscriptionsButton'],
];

// Palettes
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'subscription_override';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'subscription_waitingList';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'subscription_memberGroupsLimit';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['subscription_override'] = 'subscription_types,subscription_memberGroupsLimit,subscription_maximum,subscription_subscribeEndTime,subscription_unsubscribeEndTime,subscription_numberOfParticipants,subscription_waitingList';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['subscription_waitingList'] = 'subscription_waitingListLimit';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['subscription_memberGroupsLimit'] = 'subscription_memberGroups';

// Fields
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_override'] = [
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_types'] = [
    'filter' => true,
    'inputType' => 'checkbox',
    'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer', 'getTypes'],
    'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['typeRef'],
    'eval' => ['mandatory' => true, 'multiple' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BLOB, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_memberGroupsLimit'] = [
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_memberGroups'] = [
    'filter' => true,
    'inputType' => 'checkbox',
    'flag' => 1,
    'foreignKey' => 'tl_member_group.name',
    'eval' => ['mandatory' => true, 'multiple' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BLOB, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_maximum'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'digit', 'tl_class' => 'clr'],
    'sql' => ['type' => Types::SMALLINT, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_subscribeEndTime'] = [
    'inputType' => 'timePeriod',
    'options' => ['seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years'],
    'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_timeRef'],
    'eval' => ['rgxp' => 'natural', 'minval' => 1, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::STRING, 'length' => 64, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_unsubscribeEndTime'] = [
    'inputType' => 'timePeriod',
    'options' => ['seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years'],
    'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_timeRef'],
    'eval' => ['rgxp' => 'natural', 'minval' => 1, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::STRING, 'length' => 64, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_numberOfParticipants'] = [
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_waitingList'] = [
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_waitingListLimit'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'digit', 'tl_class' => 'clr'],
    'sql' => ['type' => Types::SMALLINT, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_lastNotificationSent'] = [
    'eval' => ['rgxp' => 'datim', 'doNotCopy' => true],
    'sql' => ['type' => Types::STRING, 'length' => 10, 'default' => ''],
];
