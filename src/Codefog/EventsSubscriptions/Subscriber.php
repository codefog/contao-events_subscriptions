<?php

namespace Codefog\EventsSubscriptions;

use Codefog\EventsSubscriptions\Event\SubscribeEvent;
use Codefog\EventsSubscriptions\Event\UnsubscribeEvent;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;

class Subscriber
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var SubscriptionValidator
     */
    private $validator;

    /**
     * Subscriber constructor.
     *
     * @param EventDispatcher       $eventDispatcher
     * @param SubscriptionValidator $validator
     */
    public function __construct(EventDispatcher $eventDispatcher, SubscriptionValidator $validator)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->validator       = $validator;
    }

    /**
     * Subscribe member to the event
     *
     * @param int $eventId
     * @param int $memberId
     *
     * @return SubscriptionModel
     */
    public function subscribeMember($eventId, $memberId)
    {
        if (!$this->validator->canMemberSubscribe(EventConfig::create($eventId), $memberId)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The member ID "%s" cannot be subscribed to event ID "%s"',
                    $memberId,
                    $eventId
                )
            );
        }

        $model         = new SubscriptionModel();
        $model->tstamp = time();
        $model->pid    = $eventId;
        $model->member = $memberId;
        $model->save();

        // Dispatch the event
        $this->eventDispatcher->dispatch(EventDispatcher::EVENT_ON_SUBSCRIBE, new SubscribeEvent($model));

        return $model;
    }

    /**
     * Unsubscribe member from the event
     *
     * @param int $eventId
     * @param int $memberId
     *
     * @return SubscriptionModel
     */
    public function unsubscribeMember($eventId, $memberId)
    {
        if (!$this->validator->isMemberSubscribed(EventConfig::create($eventId), $memberId)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The member ID "%s" is not subscribed to event ID "%s"',
                    $memberId,
                    $eventId
                )
            );
        }

        $model = SubscriptionModel::findOneBy(['pid=? AND member=?'], [$eventId, $memberId]);
        $model->delete();

        // Dispatch the event
        $this->eventDispatcher->dispatch(EventDispatcher::EVENT_ON_UNSUBSCRIBE, new UnsubscribeEvent($model));

        return $model;
    }
}
