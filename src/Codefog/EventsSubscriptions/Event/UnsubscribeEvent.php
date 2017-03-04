<?php

namespace Codefog\EventsSubscriptions\Event;

use Codefog\EventsSubscriptions\Model\SubscriptionModel;

class UnsubscribeEvent
{
    /**
     * @var SubscriptionModel
     */
    private $subscription;

    /**
     * SubscribeEvent constructor.
     *
     * @param SubscriptionModel $subscription
     */
    public function __construct(SubscriptionModel $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @return SubscriptionModel
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param SubscriptionModel $subscription
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
    }
}
