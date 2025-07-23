<?php

// Backend modules
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_events_subscription';

$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_export'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\ExportController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_exportCalendar'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\ExportCalendarController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_newFromMemberGroup'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\NewFromMemberGroupController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_notification'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\NotificationController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_overview'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\OverviewController',
    'run',
];

// Frontend modules
$GLOBALS['FE_MOD']['events']['event_list_subscribe'] = 'Codefog\EventsSubscriptionsBundle\FrontendModule\EventListModule';
$GLOBALS['FE_MOD']['events']['event_reader_subscribe'] = 'Codefog\EventsSubscriptionsBundle\FrontendModule\EventReaderModule';
$GLOBALS['FE_MOD']['events']['event_subscribe'] = 'Codefog\EventsSubscriptionsBundle\FrontendModule\EventSubscribeModule';
$GLOBALS['FE_MOD']['events']['event_subscriptions'] = 'Codefog\EventsSubscriptionsBundle\FrontendModule\EventSubscriptionsModule';

// Hooks
$GLOBALS['TL_HOOKS'][\Codefog\EventsSubscriptionsBundle\EventDispatcher::EVENT_ON_SUBSCRIBE][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\NotificationListener',
    'onSubscribe',
];
$GLOBALS['TL_HOOKS'][\Codefog\EventsSubscriptionsBundle\EventDispatcher::EVENT_ON_UNSUBSCRIBE][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\NotificationListener',
    'onUnsubscribe',
];

$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\InsertTagsListener',
    'onReplace',
];

$GLOBALS['TL_HOOKS']['generatePage'][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\PageListener',
    'onGeneratePage',
];

$GLOBALS['TL_HOOKS']['reviseTable'][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\TableListener',
    'onReviseTable',
];

$GLOBALS['TL_HOOKS']['getAllEvents'][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\EventsListener',
    'onGetAllEvents',
];

// Models
$GLOBALS['TL_MODELS']['tl_calendar_events_subscription'] = 'Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel';

// Cron jobs
$GLOBALS['TL_CRON']['hourly'][] = ['Codefog\EventsSubscriptionsBundle\EventListener\CronListener', 'onHourlyJob'];

/**
 * Add the subscription types
 */
\Codefog\EventsSubscriptionsBundle\Services::getSubscriptionFactory()->add(
    'guest',
    'Codefog\EventsSubscriptionsBundle\Subscription\GuestSubscription'
);

\Codefog\EventsSubscriptionsBundle\Services::getSubscriptionFactory()->add(
    'member',
    'Codefog\EventsSubscriptionsBundle\Subscription\MemberSubscription'
);
