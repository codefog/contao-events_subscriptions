<?php

namespace Codefog\EventsSubscriptions;

use Codefog\EventsSubscriptions\Model\SubscriptionModel;

class SubscriptionValidator
{
    /**
     * Return true if the member can subscribe to the event
     *
     * @param EventConfig $config
     * @param int         $memberId
     *
     * @return bool
     */
    public function canMemberSubscribe(EventConfig $config, $memberId)
    {
        if ($this->isMemberSubscribed($config, $memberId)) {
            return false;
        }

        if (!$this->validateMaximumSubscriptions($config)) {
            return false;
        }

        if (!$this->validateSubscribeEndTime($config)) {
            return false;
        }

        return true;
    }

    /**
     * Return true if the member can unsubscribe from the event
     *
     * @param EventConfig $config
     * @param int         $memberId
     *
     * @return bool
     */
    public function canMemberUnsubscribe(EventConfig $config, $memberId)
    {
        if (!$this->isMemberSubscribed($config, $memberId)) {
            return false;
        }

        if (!$this->validateUnsubscribeEndTime($config)) {
            return false;
        }

        return true;
    }

    /**
     * Return true if member is subscribed
     *
     * @param int $eventId
     *
     * @return bool
     */
    public function isMemberSubscribed(EventConfig $config, $memberId)
    {
        if (!$config->canSubscribe()) {
            return false;
        }

        return SubscriptionModel::findOneBy(['pid=? AND member=?'], [$config->getEvent()->id, $memberId]) !== null;
    }

    /**
     * Validate the maximum number of subscriptions
     *
     * @param EventConfig $config
     *
     * @return bool
     */
    public function validateMaximumSubscriptions(EventConfig $config)
    {
        if (!$config->canSubscribe()) {
            return false;
        }

        // Value is not set, unlimited number of subscriptions
        if (!($max = $config->getMaximumSubscriptions())) {
            return true;
        }

        return SubscriptionModel::countBy('pid', $config->getEvent()->id) < $max;
    }

    /**
     * Validate the subscribe end time
     *
     * @param EventConfig $config
     *
     * @return bool
     */
    public function validateSubscribeEndTime(EventConfig $config)
    {
        if (!$config->canSubscribe()) {
            return false;
        }

        return $config->getSubscribeEndTime() > time();
    }

    /**
     * Validate the unsubscribe end time
     *
     * @param EventConfig $config
     *
     * @return bool
     */
    public function validateUnsubscribeEndTime(EventConfig $config)
    {
        if (!$config->canSubscribe()) {
            return false;
        }

        return $config->getUnsubscribeEndTime() > time();
    }
}
