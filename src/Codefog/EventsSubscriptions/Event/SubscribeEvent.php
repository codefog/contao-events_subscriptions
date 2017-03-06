<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\Event;

use Codefog\EventsSubscriptions\Model\SubscriptionModel;

class SubscribeEvent
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
