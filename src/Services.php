<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle;

class Services
{
    /**
     * Instances
     * @var array
     */
    private static $instances = [];

    /**
     * Initializations
     * @var array
     */
    private static $initializations = [];

    /**
     * Get the automator
     *
     * @return Automator
     */
    public static function getAutomator()
    {
        return static::get(
            'automator',
            function () {
                return new Automator(static::getNotificationSender(), static::getSubscriptionFactory());
            }
        );
    }

    /**
     * Get the event dispatcher
     *
     * @return EventDispatcher
     */
    public static function getEventDispatcher()
    {
        return static::get(
            'event-dispatcher',
            function () {
                return new EventDispatcher();
            }
        );
    }

    /**
     * Get the exporter
     *
     * @return Exporter
     */
    public static function getExporter()
    {
        return static::get(
            'exporter',
            function () {
                return new Exporter(static::getEventDispatcher(), static::getSubscriptionFactory());
            }
        );
    }

    /**
     * Get the flash message
     *
     * @return FlashMessage
     */
    public static function getFlashMessage()
    {
        return static::get(
            'flash-message',
            function () {
                return new FlashMessage();
            }
        );
    }

    /**
     * Get the notification sender
     *
     * @return NotificationSender
     */
    public static function getNotificationSender()
    {
        return static::get(
            'notification-sender',
            function () {
                return new NotificationSender(static::getSubscriptionFactory());
            }
        );
    }

    /**
     * Get the subscriber
     *
     * @return Subscriber
     */
    public static function getSubscriber()
    {
        return static::get(
            'subscriber',
            function () {
                return new Subscriber(
                    static::getEventDispatcher(),
                    static::getSubscriptionFactory(),
                    static::getSubscriptionValidator()
                );
            }
        );
    }

    /**
     * Get the event config factory
     *
     * @return EventConfigFactory
     */
    public static function getEventConfigFactory()
    {
        return static::get(
            'event-config-factory',
            function () {
                return new EventConfigFactory();
            }
        );
    }

    /**
     * Get the subscription factory
     *
     * @return SubscriptionFactory
     */
    public static function getSubscriptionFactory()
    {
        return static::get(
            'subscription-factory',
            function () {
                return new SubscriptionFactory();
            }
        );
    }

    /**
     * Get the subscription validator
     *
     * @return SubscriptionValidator
     */
    public static function getSubscriptionValidator()
    {
        return static::get(
            'subscription-validator',
            function () {
                return new SubscriptionValidator();
            }
        );
    }

    /**
     * Set the initialization
     *
     * @param string $key
     * @param callable $init
     */
    public static function setInitialization($key, callable $init)
    {
        static::$initializations[$key] = $init;
    }

    /**
     * Get the instance
     *
     * @param string   $key
     * @param callable $init
     *
     * @return object
     */
    private static function get($key, callable $init)
    {
        if (!isset(static::$instances[$key])) {
            // Set the initialization instead of the default value
            if (isset(static::$initializations[$key]) && is_callable(static::$initializations[$key])) {
                $init = static::$initializations[$key];
            }

            static::$instances[$key] = $init();
        }

        return static::$instances[$key];
    }
}
