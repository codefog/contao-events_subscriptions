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
 * Register PSR-0 namespace
 */
NamespaceClassLoader::add('Codefog\EventsSubscriptions', 'system/modules/events_subscriptions/src');

/**
 * Register the templates
 */
TemplateLoader::addFiles(
    [
        'event_list_subscribe'   => 'system/modules/events_subscriptions/templates/events',
        'mod_eventsubscribe'     => 'system/modules/events_subscriptions/templates/modules',
        'mod_eventlistsubscribe' => 'system/modules/events_subscriptions/templates/modules',
        'mod_eventsubscriptions' => 'system/modules/events_subscriptions/templates/modules',
    ]
);
