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

        // Partials
        'eventsubscription_form'   => 'system/modules/events_subscriptions/templates/partials',
    ]
);
