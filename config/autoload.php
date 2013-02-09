<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 * 
 * Copyright (C) 2013 Codefog
 * 
 * @package events_subscriptions
 * @link    http://codefog.pl
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */


/**
 * Register a custom namespace
 */
ClassLoader::addNamespace('EventsSubscriptions');


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'EventsSubscriptions\EventsSubscriptions'      => 'system/modules/events_subscriptions/classes/EventsSubscriptions.php',

	// Modules
	'EventsSubscriptions\ModuleEventSubscribe'     => 'system/modules/events_subscriptions/modules/ModuleEventSubscribe.php',
	'EventsSubscriptions\ModuleEventListSubscribe' => 'system/modules/events_subscriptions/modules/ModuleEventListSubscribe.php'
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'event_list_subscribe'   => 'system/modules/events_subscriptions/templates',
	'mod_eventsubscribe'     => 'system/modules/events_subscriptions/templates',
	'mod_eventlistsubscribe' => 'system/modules/events_subscriptions/templates'
));
