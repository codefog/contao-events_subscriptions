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
if (class_exists('NamespaceClassLoader')) {
    NamespaceClassLoader::add('Codefog\EventsSubscriptions', 'system/modules/events_subscriptions/src');
}

/**
 * Register the templates
 */
TemplateLoader::addFiles(
    [
        // Events
        'event_list_subscribe'     => 'system/modules/events_subscriptions/templates/events',

        // Frontend modules
        'mod_event_list_subscribe' => 'system/modules/events_subscriptions/templates/modules',
        'mod_event_subscribe'      => 'system/modules/events_subscriptions/templates/modules',
        'mod_event_subscriptions'  => 'system/modules/events_subscriptions/templates/modules',
    ]
);
