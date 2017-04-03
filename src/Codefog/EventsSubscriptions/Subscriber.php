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

use Codefog\EventsSubscriptions\Event\SubscribeEvent;
use Codefog\EventsSubscriptions\Event\UnsubscribeEvent;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Codefog\EventsSubscriptions\Subscription\SubscriptionInterface;

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
        $model->save();

        // Dispatch the event
        $this->eventDispatcher->dispatch(
            EventDispatcher::EVENT_ON_SUBSCRIBE,
            new SubscribeEvent($model, $subscription)
        );

        return $model;
    }

    /**
     * Unsubscribe user from the event
     *
     * @param int $eventId
     * @param int $memberId
     *
     * @return SubscriptionModel
     */
    public function unsubscribe(EventConfig $event, SubscriptionInterface $subscription)
    {
        $model = SubscriptionModel::findOneBy(
            ['pid=? AND type=?'],
            [$event->getEvent()->id, $this->factory->getType(get_class($subscription))]
        );

        $model->delete();

        // Dispatch the event
        $this->eventDispatcher->dispatch(
            EventDispatcher::EVENT_ON_UNSUBSCRIBE,
            new UnsubscribeEvent($model, $subscription)
        );

        return $model;
    }
}
