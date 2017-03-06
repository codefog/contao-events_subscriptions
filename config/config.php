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
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_events_subscription';

/**
 * Add front end modules
 */
$GLOBALS['FE_MOD']['events']['event_list_subscribe'] = 'Codefog\EventsSubscriptions\FrontendModule\EventListModule';
$GLOBALS['FE_MOD']['events']['event_subscribe']      = 'Codefog\EventsSubscriptions\FrontendModule\EventSubscribeModule';
$GLOBALS['FE_MOD']['events']['event_subscriptions']  = 'Codefog\EventsSubscriptions\FrontendModule\EventSubscriptionsModule';

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

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_calendar_events_subscription'] = 'Codefog\EventsSubscriptions\Model\SubscriptionModel';

/**
 * Cron jobs
 */
$GLOBALS['TL_CRON']['hourly'][] = ['Codefog\EventsSubscriptions\EventListener\CronListener', 'onHourlyJob'];

/**
 * Notification Center Notification Types
 */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['events_subscriptions'] = [
    'events_subscriptions_reminder'    => [
        'recipients'           => ['admin_email', 'member_email'],
        'email_subject'        => ['admin_email', 'member_email', 'member_*', 'event_*', 'calendar_*'],
        'email_text'           => ['admin_email', 'member_email', 'member_*', 'event_*', 'calendar_*'],
        'email_html'           => ['admin_email', 'member_email', 'member_*', 'event_*', 'calendar_*'],
        'email_sender_name'    => ['admin_email', 'member_email'],
        'email_sender_address' => ['admin_email', 'member_email'],
        'email_recipient_cc'   => ['admin_email', 'member_email'],
        'email_recipient_bcc'  => ['admin_email', 'member_email'],
        'email_replyTo'        => ['admin_email', 'member_email'],
    ],
    'events_subscriptions_subscribe'   => [
        'recipients'           => ['admin_email', 'member_email'],
        'email_subject'        => ['admin_email', 'member_email', 'member_*', 'event_*', 'calendar_*'],
        'email_text'           => ['admin_email', 'member_email', 'member_*', 'event_*', 'calendar_*'],
        'email_html'           => ['admin_email', 'member_email', 'member_*', 'event_*', 'calendar_*'],
        'email_sender_name'    => ['admin_email', 'member_email'],
        'email_sender_address' => ['admin_email', 'member_email'],
        'email_recipient_cc'   => ['admin_email', 'member_email'],
        'email_recipient_bcc'  => ['admin_email', 'member_email'],
        'email_replyTo'        => ['admin_email', 'member_email'],
    ],
    'events_subscriptions_unsubscribe' => [
        'recipients'           => ['admin_email', 'member_email'],
        'email_subject'        => ['admin_email', 'member_email', 'member_*', 'event_*', 'calendar_*'],
        'email_text'           => ['admin_email', 'member_email', 'member_*', 'event_*', 'calendar_*'],
        'email_html'           => ['admin_email', 'member_email', 'member_*', 'event_*', 'calendar_*'],
        'email_sender_name'    => ['admin_email', 'member_email'],
        'email_sender_address' => ['admin_email', 'member_email'],
        'email_recipient_cc'   => ['admin_email', 'member_email'],
        'email_recipient_bcc'  => ['admin_email', 'member_email'],
        'email_replyTo'        => ['admin_email', 'member_email'],
    ],
];
