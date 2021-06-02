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
 * Add a back end table to calendar
 */
$GLOBALS['BE_MOD']['content']['calendar']['tables'][]             = 'tl_calendar_events_subscription';
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_export'] = [
    'Codefog\EventsSubscriptions\Backend\ExportController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_newFromMemberGroup'] = [
    'Codefog\EventsSubscriptions\Backend\NewFromMemberGroupController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_notification'] = [
    'Codefog\EventsSubscriptions\Backend\NotificationController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_overview'] = [
    'Codefog\EventsSubscriptions\Backend\OverviewController',
    'run',
];

/**
 * Add front end modules
 */
$GLOBALS['FE_MOD']['events']['event_list_subscribe']   = 'Codefog\EventsSubscriptions\FrontendModule\EventListModule';
$GLOBALS['FE_MOD']['events']['event_reader_subscribe'] = 'Codefog\EventsSubscriptions\FrontendModule\EventReaderModule';
$GLOBALS['FE_MOD']['events']['event_subscribe']        = 'Codefog\EventsSubscriptions\FrontendModule\EventSubscribeModule';
$GLOBALS['FE_MOD']['events']['event_subscriptions']    = 'Codefog\EventsSubscriptions\FrontendModule\EventSubscriptionsModule';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS'][\Codefog\EventsSubscriptions\EventDispatcher::EVENT_ON_SUBSCRIBE][]   = [
    'Codefog\EventsSubscriptions\EventListener\NotificationListener',
    'onSubscribe',
];
$GLOBALS['TL_HOOKS'][\Codefog\EventsSubscriptions\EventDispatcher::EVENT_ON_UNSUBSCRIBE][] = [
    'Codefog\EventsSubscriptions\EventListener\NotificationListener',
    'onUnsubscribe',
];

$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = [
    'Codefog\EventsSubscriptions\EventListener\InsertTagsListener',
    'onReplace',
];

$GLOBALS['TL_HOOKS']['generatePage'][] = [
    'Codefog\EventsSubscriptions\EventListener\PageListener',
    'onGeneratePage',
];

$GLOBALS['TL_HOOKS']['reviseTable'][] = [
    'Codefog\EventsSubscriptions\EventListener\TableListener',
    'onReviseTable',
];

$GLOBALS['TL_HOOKS']['getAllEvents'][] = [
    'Codefog\EventsSubscriptions\EventListener\EventsListener',
    'onGetAllEvents',
];

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_calendar_events_subscription'] = 'Codefog\EventsSubscriptions\Model\SubscriptionModel';

/**
 * Cron jobs
 */
$GLOBALS['TL_CRON']['hourly'][] = ['Codefog\EventsSubscriptions\EventListener\CronListener', 'onHourlyJob'];

/**
 * Add the subscription types
 */
\Codefog\EventsSubscriptions\Services::getSubscriptionFactory()->add(
    'guest',
    'Codefog\EventsSubscriptions\Subscription\GuestSubscription'
);

\Codefog\EventsSubscriptions\Services::getSubscriptionFactory()->add(
    'member',
    'Codefog\EventsSubscriptions\Subscription\MemberSubscription'
);

/**
 * Notification Center Notification Types
 */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['events_subscriptions'] = [
    'events_subscriptions_reminder'    => [
        'recipients'           => ['admin_email', 'recipient_email'],
        'email_subject'        => ['admin_email', 'recipient_email', 'subscription_*', 'event_*', 'calendar_*'],
        'email_text'           => ['admin_email', 'recipient_email', 'unsubscribe_link', 'subscription_*', 'event_*', 'event_link', 'calendar_*'],
        'email_html'           => ['admin_email', 'recipient_email', 'unsubscribe_link', 'subscription_*', 'event_*', 'event_link', 'calendar_*'],
        'email_sender_name'    => ['admin_email', 'recipient_email'],
        'email_sender_address' => ['admin_email', 'recipient_email'],
        'email_recipient_cc'   => ['admin_email', 'recipient_email'],
        'email_recipient_bcc'  => ['admin_email', 'recipient_email'],
        'email_replyTo'        => ['admin_email', 'recipient_email'],
    ],
    'events_subscriptions_subscribe'   => [
        'recipients'           => ['admin_email', 'recipient_email'],
        'email_subject'        => ['admin_email', 'recipient_email', 'subscription_*', 'event_*', 'calendar_*'],
        'email_text'           => ['admin_email', 'recipient_email', 'unsubscribe_link', 'subscription_*', 'event_*', 'event_link', 'calendar_*'],
        'email_html'           => ['admin_email', 'recipient_email', 'unsubscribe_link', 'subscription_*', 'event_*', 'event_link', 'calendar_*'],
        'email_sender_name'    => ['admin_email', 'recipient_email'],
        'email_sender_address' => ['admin_email', 'recipient_email'],
        'email_recipient_cc'   => ['admin_email', 'recipient_email'],
        'email_recipient_bcc'  => ['admin_email', 'recipient_email'],
        'email_replyTo'        => ['admin_email', 'recipient_email'],
    ],
    'events_subscriptions_unsubscribe' => [
        'recipients'           => ['admin_email', 'recipient_email'],
        'email_subject'        => ['admin_email', 'recipient_email', 'subscription_*', 'event_*', 'calendar_*'],
        'email_text'           => ['admin_email', 'recipient_email', 'subscription_*', 'event_*', 'event_link', 'calendar_*'],
        'email_html'           => ['admin_email', 'recipient_email', 'subscription_*', 'event_*', 'event_link', 'calendar_*'],
        'email_sender_name'    => ['admin_email', 'recipient_email'],
        'email_sender_address' => ['admin_email', 'recipient_email'],
        'email_recipient_cc'   => ['admin_email', 'recipient_email'],
        'email_recipient_bcc'  => ['admin_email', 'recipient_email'],
        'email_replyTo'        => ['admin_email', 'recipient_email'],
    ],
    'events_subscriptions_listUpdate' => [
        'recipients'           => ['admin_email', 'recipient_email'],
        'email_subject'        => ['admin_email', 'recipient_email', 'subscription_*', 'event_*', 'calendar_*'],
        'email_text'           => ['admin_email', 'recipient_email', 'unsubscribe_link', 'subscription_*', 'event_*', 'event_link', 'calendar_*'],
        'email_html'           => ['admin_email', 'recipient_email', 'unsubscribe_link', 'subscription_*', 'event_*', 'event_link', 'calendar_*'],
        'email_sender_name'    => ['admin_email', 'recipient_email'],
        'email_sender_address' => ['admin_email', 'recipient_email'],
        'email_recipient_cc'   => ['admin_email', 'recipient_email'],
        'email_recipient_bcc'  => ['admin_email', 'recipient_email'],
        'email_replyTo'        => ['admin_email', 'recipient_email'],
    ],
    'events_subscription_event' => [
        'recipients'           => ['admin_email', 'recipient_email'],
        'email_subject'        => ['admin_email', 'recipient_email', 'event_*', 'calendar_*', 'member_*'],
        'email_text'           => ['admin_email', 'recipient_email', 'event_*', 'event_link', 'calendar_*', 'member_*'],
        'email_html'           => ['admin_email', 'recipient_email', 'event_*', 'event_link', 'calendar_*', 'member_*'],
        'email_sender_name'    => ['admin_email', 'recipient_email'],
        'email_sender_address' => ['admin_email', 'recipient_email'],
        'email_recipient_cc'   => ['admin_email', 'recipient_email'],
        'email_recipient_bcc'  => ['admin_email', 'recipient_email'],
        'email_replyTo'        => ['admin_email', 'recipient_email'],
    ],
];
