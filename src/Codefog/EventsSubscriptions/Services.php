<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions;

class Services
{
    /**
     * Instances
     * @var array
     */
    private static $instances = [];

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
                return new Automator(static::getNotificationSender());
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
            static::$instances[$key] = $init();
        }

        return static::$instances[$key];
    }
}
