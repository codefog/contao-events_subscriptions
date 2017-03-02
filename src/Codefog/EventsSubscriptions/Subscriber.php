<?php

namespace Codefog\EventsSubscriptions;

use Codefog\EventsSubscriptions\Model\SubscriptionModel;

class Subscriber
{
    /**
     * @var SubscriptionValidator
     */
    private $validator;

    /**
     * Subscriber constructor.
     */
    public function __construct()
    {
        $this->validator = new SubscriptionValidator();
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

        return $model->save();
    }

    /**
     * Unsubscribe member from the event
     *
     * @param int $eventId
     * @param int $memberId
     *
     * @return bool
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

        return $model->delete() ? true : false;
    }
}
