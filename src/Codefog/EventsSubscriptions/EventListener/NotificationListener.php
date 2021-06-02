<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\EventListener;

use Codefog\EventsSubscriptions\Event\SubscribeEvent;
use Codefog\EventsSubscriptions\Event\UnsubscribeEvent;
use Codefog\EventsSubscriptions\EventConfigFactory;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Codefog\EventsSubscriptions\NotificationSender;
use Codefog\EventsSubscriptions\Services;

class NotificationListener
{
    /**
     * @var EventConfigFactory
     */
    private $eventConfigFactory;

    /**
     * @var NotificationSender
     */
    private $sender;

    /**
     * NotificationListener constructor.
     */
    public function __construct()
    {
        $this->eventConfigFactory = Services::getEventConfigFactory();
        $this->sender = Services::getNotificationSender();
    }

    /**
     * On subscribe to event
     *
     * @param SubscribeEvent $event
     */
    public function onSubscribe(SubscribeEvent $event)
    {
        $extras = $event->getExtras();

        // Do not set notification if explicitly set not to do so
        if (isset($extras['notification']) && $extras['notification'] === false) {
            return;
        }

        $this->sender->sendByType('events_subscriptions_subscribe', $event->getModel());
    }

    /**
     * On unsubscribe to event
     *
     * @param UnsubscribeEvent $event
     */
    public function onUnsubscribe(UnsubscribeEvent $event)
    {
        $this->sender->sendByType('events_subscriptions_unsubscribe', $event->getModel());

        // Get the waiting list promoted subscription
        if (($subscriptionModel = $this->getWaitingListPromotedSubscription($event)) !== null) {
            $this->sender->sendByType('events_subscriptions_listUpdate', $subscriptionModel);
        }
    }

    /**
     * Get the subscription model that has moved from waiting list to subscriber list because of the current event.
     *
     * @param UnsubscribeEvent $event
     *
     * @return SubscriptionModel|null
     */
    private function getWaitingListPromotedSubscription(UnsubscribeEvent $event)
    {
        if ($event->getSubscription()->isOnWaitingList()) {
            return null;
        }

        $unsubscribedModel = $event->getModel();
        $eventConfig = $this->eventConfigFactory->create($unsubscribedModel->pid);

        // Return null if the event has no waiting list
        if (!$eventConfig->hasWaitingList() || ($maxSubscriptions = $eventConfig->getMaximumSubscriptions()) === 0) {
            return null;
        }

        $newerSubscriptions = SubscriptionModel::countBy(['id!=?', 'pid=?', 'dateCreated>=?'], [$unsubscribedModel->id, $unsubscribedModel->pid, $unsubscribedModel->dateCreated]);

        // Return null if there are no newer subscriptions (i.e. no subscription to be promoted)
        if ($newerSubscriptions === 0) {
            return null;
        }

        $olderSubscriptions = SubscriptionModel::countBy(['pid=?', 'dateCreated<?'], [$unsubscribedModel->pid, $unsubscribedModel->dateCreated]);

        // Return null if there are completely no other subscriptions
        if ($newerSubscriptions === 0 && $olderSubscriptions === 0) {
            return null;
        }

        // Return null if the limit is smaller or equal than the current number of subscriptions (can happen if the existing limit is changed)
        if ($maxSubscriptions <= $olderSubscriptions) {
            return null;
        }

        return SubscriptionModel::findOneBy(['pid=?'], [$unsubscribedModel->pid], ['offset' => $maxSubscriptions]);
    }
}
