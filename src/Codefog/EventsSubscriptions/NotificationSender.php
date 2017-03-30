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

use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Codefog\EventsSubscriptions\Subscription\NotificationAwareInterface;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\Config;
use Contao\Model;
use Contao\Model\Collection;
use Haste\Util\Format;
use NotificationCenter\Model\Notification;

class NotificationSender
{
    /**
     * @var SubscriptionFactory
     */
    private $factory;

    /**
     * NotificationSender constructor.
     *
     * @param SubscriptionFactory $factory
     */
    public function __construct(SubscriptionFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Send the notification
     *
     * @param Notification      $notification
     * @param SubscriptionModel $model
     */
    public function send(Notification $notification, SubscriptionModel $model)
    {
        $subscription = $this->factory->createFromModel($model);

        if (!($subscription instanceof NotificationAwareInterface) || ($event = $model->getEvent()) === null) {
            return;
        }

        $tokens = $this->generateTokens($event, $subscription);
        $notification->send($tokens, $tokens['subscription_language']);
    }

    /**
     * Send the notification by type
     *
     * @param string            $type
     * @param SubscriptionModel $model
     */
    public function sendByType($type, SubscriptionModel $model)
    {
        if (($notifications = Notification::findBy('type', $type)) === null) {
            return;
        }

        /**
         * @var Collection   $notifications
         * @var Notification $notification
         */
        foreach ($notifications as $notification) {
            $this->send($notification, $model);
        }
    }

    /**
     * Generate the tokens
     *
     * @param CalendarEventsModel        $event
     * @param NotificationAwareInterface $subscription
     *
     * @return array
     */
    private function generateTokens(CalendarEventsModel $event, NotificationAwareInterface $subscription)
    {
        $tokens                    = $subscription->getNotificationTokens();
        $tokens['admin_email']     = $GLOBALS['TL_ADMIN_EMAIL'] ?: Config::get('adminEmail');
        $tokens['recipient_email'] = $subscription->getNotificationEmail();

        // Generate event tokens
        $tokens = array_merge($tokens, $this->getModelTokens($event, 'event_'));

        // Generate calendar tokens
        if (($calendar = CalendarModel::findByPk($event->pid)) !== null) {
            $tokens = array_merge($tokens, $this->getModelTokens($calendar, 'calendar_'));
        }

        return $tokens;
    }

    /**
     * Get the model tokens
     *
     * @param Model  $model
     * @param string $prefix
     *
     * @return array
     */
    private function getModelTokens(Model $model, $prefix)
    {
        $tokens = [];
        $table  = $model::getTable();

        foreach ($model->row() as $k => $v) {
            $tokens[$prefix.$k] = Format::dcaValue($table, $k, $v);
        }

        return $tokens;
    }
}
