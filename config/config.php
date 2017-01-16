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
 * Extension version
 */
@define('EVENTS_SUBSCRIPTIONS_VERSION', '1.0');
@define('EVENTS_SUBSCRIPTIONS_BUILD', '6');

/**
 * Add a back end table to calendar
 */
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_events_subscriptions';

/**
 * Add front end modules
 */
$GLOBALS['FE_MOD']['events']['eventsubscribe']     = 'Codefog\EventsSubscriptions\FrontendModule\SubscribeModule';
$GLOBALS['FE_MOD']['events']['eventlistsubscribe'] = 'Codefog\EventsSubscriptions\FrontendModule\EventListModule';

/**
 * Cron jobs
 */
$GLOBALS['TL_CRON']['hourly'][] = ['Codefog\EventsSubscriptions\EventListener\CronListener', 'onHourlyJob'];

/**
 * Notification Center Notification Types
 */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['events_subscriptions'] = [
    'events_subscriptions_reminder' => [
        'recipients'           => ['admin_email', 'member_email'],
        'email_subject'        => ['admin_email', 'member_email', 'member_*', 'event_*'],
        'email_text'           => ['admin_email', 'member_email', 'member_*', 'event_*'],
        'email_html'           => ['admin_email', 'member_email', 'member_*', 'event_*'],
        'email_sender_name'    => ['admin_email', 'member_email'],
        'email_sender_address' => ['admin_email', 'member_email'],
        'email_recipient_cc'   => ['admin_email', 'member_email'],
        'email_recipient_bcc'  => ['admin_email', 'member_email'],
        'email_replyTo'        => ['admin_email', 'member_email'],
    ],
];
