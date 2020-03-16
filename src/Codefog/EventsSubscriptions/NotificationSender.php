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
use Codefog\EventsSubscriptions\Subscription\SubscriptionInterface;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\Config;
use Contao\Events;
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
        // Check if there is a custom notification set in the calendar settings
        if (($event = $model->getEvent()) !== null && ($calendar = $event->getRelated('pid')) !== null) {
            $notificationType = substr($type, 21); // strip events_subscriptions_ prefix
            $field = sprintf('subscription_%sNotification', $notificationType);

            if ($calendar->$field && ($notification = Notification::findByPk($calendar->$field)) !== null) {
                $this->send($notification, $model);

                return;
            }
        }

        // Otherwise send all notifications of certain type
        if (($notifications = Notification::findBy('type', $type)) !== null) {
            /**
             * @var Collection   $notifications
             * @var Notification $notification
             */
            foreach ($notifications as $notification) {
                $this->send($notification, $model);
            }
        }
    }

    /**
     * Get the tokens
     *
     * @param array $data
     * @param string $table
     * @param string $prefix
     *
     * @return array
     */
    public function getTokens(array $data, $table, $prefix)
    {
        $tokens = [];

        foreach ($data as $k => $v) {
            $tokens[$prefix.$k] = Format::dcaValue($table, $k, $v);
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
    public function getModelTokens(Model $model, $prefix)
    {
        return $this->getTokens($model->row(), $model::getTable(), $prefix);
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

        // Waiting list token
        if ($subscription instanceof SubscriptionInterface) {
            $tokens['subscription_waitingList'] = $subscription->isOnWaitingList();
        }

        // Generate event tokens
        $tokens = array_merge($tokens, $this->getModelTokens($event, 'event_'));
        $tokens['event_link'] = Events::generateEventUrl($event, true);

        // Generate calendar tokens
        if (($calendar = CalendarModel::findByPk($event->pid)) !== null) {
            $tokens = array_merge($tokens, $this->getModelTokens($calendar, 'calendar_'));
        }

        return $tokens;
    }
}
