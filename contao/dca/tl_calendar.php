<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Doctrine\DBAL\Types\Types;

// Load tl_calendar_events data container
\Contao\Controller::loadDataContainer('tl_calendar_events');
\Contao\System::loadLanguageFile('tl_calendar_events');

// List operations
$GLOBALS['TL_DCA']['tl_calendar']['list']['operations']['subscriptions_overview'] = [
    'href' => 'key=subscriptions_overview',
    'icon' => 'mgroup.svg',
    'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'getSubscriptionsOverviewButton'],
];

// Palettes
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_enable';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_waitingList';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_memberGroupsLimit';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_reminders';

PaletteManipulator::create()
    ->addLegend('subscription_legend', 'title_legend', PaletteManipulator::POSITION_AFTER, true)
    ->addField('subscription_enable', 'subscription_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar');

$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_enable'] = 'subscription_types,subscription_memberGroupsLimit,subscription_maximum,subscription_subscribeEndTime,subscription_unsubscribeEndTime,subscription_numberOfParticipants,subscription_waitingList,subscription_reminders,subscription_unsubscribeLinkPage,subscription_subscribeNotification,subscription_unsubscribeNotification,subscription_listUpdateNotification';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_waitingList'] = 'subscription_waitingListLimit';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_memberGroupsLimit'] = 'subscription_memberGroups';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_reminders'] = 'subscription_time,subscription_days,subscription_notification,subscription_skipWaitingListReminders';

// Fields
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_enable'] = [
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_types'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_types'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_maximum'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_maximum'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_subscribeEndTime'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_subscribeEndTime'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_unsubscribeEndTime'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_unsubscribeEndTime'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_numberOfParticipants'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_numberOfParticipants'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_waitingList'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_waitingList'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_waitingListLimit'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_waitingListLimit'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_memberGroupsLimit'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_memberGroupsLimit'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_memberGroups'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_memberGroups'];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_reminders'] = [
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_time'] = [
    'inputType' => 'text',
    'eval' => ['mandatory' => true, 'rgxp' => 'time', 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_days'] = [
    'inputType' => 'text',
    'eval' => ['mandatory' => true, 'maxlength' => 32, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::STRING, 'length' => 32, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_notification'] = [
    'inputType' => 'select',
    'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'getNotifications'],
    'eval' => ['mandatory' => true, 'includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_subscribeNotification'] = [
    'inputType' => 'select',
    'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'getSubscribeNotifications'],
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_unsubscribeNotification'] = [
    'inputType' => 'select',
    'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'getUnsubscribeNotifications'],
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_listUpdateNotification'] = [
    'inputType' => 'select',
    'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'getListUpdateNotifications'],
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_skipWaitingListReminders'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_unsubscribeLinkPage'] = [
    'inputType' => 'pageTree',
    'eval' => ['fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];
