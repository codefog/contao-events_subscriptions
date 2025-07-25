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

use Codefog\EventsSubscriptionsBundle\Event\SubscribeEvent;
use Codefog\EventsSubscriptionsBundle\Event\UnsubscribeEvent;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\Subscription\SubscriptionInterface;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;

class Subscriber
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var SubscriptionFactory
     */
    private $factory;

    /**
     * @var SubscriptionValidator
     */
    private $validator;

    /**
     * Subscriber constructor.
     *
     * @param EventDispatcher       $eventDispatcher
     * @param SubscriptionFactory   $factory
     * @param SubscriptionValidator $validator
     */
    public function __construct(
        EventDispatcher $eventDispatcher,
        SubscriptionFactory $factory,
        SubscriptionValidator $validator
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->factory         = $factory;
        $this->validator       = $validator;
    }

    /**
     * Subscribe user to the event
     *
     * @param EventConfig           $event
     * @param SubscriptionInterface $subscription
     *
     * @return SubscriptionModel
     */
    public function subscribe(EventConfig $event, SubscriptionInterface $subscription)
    {
        $now   = time();
        $model = new SubscriptionModel();
        $subscription->writeToModel($event, $model);

        // Write meta data and save
        $model->tstamp      = $now;
        $model->dateCreated = $now;
        $model->type        = $this->factory->getType(get_class($subscription));
        $model->pid         = $event->getEvent()->id;
        $model->unsubscribeToken = SubscriptionModel::generateUnsubscribeToken();
        $model->save();

        // Set the subscription model before checking if it's on waiting list
        $subscription->setSubscriptionModel($model);

        // Log the subscription
        if ($subscription->isOnWaitingList()) {
            $logMessage = '%s has subscribed to a waiting list of the event "%s" (ID %s)';
        } else {
            $logMessage = '%s has subscribed to the event "%s" (ID %s)';
        }

        System::getContainer()->get('monolog.logger.contao')->info(
            sprintf($logMessage, strip_tags($subscription->getFrontendLabel()), $event->getEvent()->title, $event->getEvent()->id),
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)],
        );

        // Dispatch the event
        $dispatchEvent = new SubscribeEvent($model, $subscription);
        $dispatchEvent->setExtras($event->getExtras());

        $this->eventDispatcher->dispatch(EventDispatcher::EVENT_ON_SUBSCRIBE, $dispatchEvent);

        return $model;
    }

    /**
     * Unsubscribe user from the event
     *
     * @param EventConfig           $event
     * @param SubscriptionInterface $subscription
     *
     * @return SubscriptionModel
     *
     * @throws \RuntimeException
     */
    public function unsubscribe(EventConfig $event, SubscriptionInterface $subscription)
    {
        $columns = ['pid=? AND type=?'];
        $values = [$event->getEvent()->id, $this->factory->getType(get_class($subscription))];
        $subscription->setUnsubscribeCriteria($event, $columns, $values);

        if (($model = SubscriptionModel::findOneBy($columns, $values)) === null) {
            throw new \RuntimeException('The subscription model to unsubscribe could not be found');
        }

        $subscription->setSubscriptionModel($model);

        // Log the unsubscription
        if ($subscription->isOnWaitingList()) {
            $logMessage = '%s has unsubscribed from a waiting list of the event "%s" (ID %s)';
        } else {
            $logMessage = '%s has unsubscribed from the event "%s" (ID %s)';
        }

        System::getContainer()->get('monolog.logger.contao')->info(
            sprintf($logMessage, strip_tags($subscription->getFrontendLabel()), $event->getEvent()->title, $event->getEvent()->id),
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)],
        );

        // First, dispatch the event (consistency with DC_Table)
        $this->eventDispatcher->dispatch(EventDispatcher::EVENT_ON_UNSUBSCRIBE, new UnsubscribeEvent($model, $subscription));

        // Delete the model
        $model->delete();

        return $model;
    }
}
