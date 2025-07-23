<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle\Event;

use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\Subscription\SubscriptionInterface;

class UnsubscribeEvent
{
    /**
     * @var SubscriptionModel
     */
    private $model;

    /**
     * @var SubscriptionInterface
     */
    private $subscription;

    /**
     * SubscribeEvent constructor.
     *
     * @param SubscriptionModel     $model
     * @param SubscriptionInterface $subscription
     */
    public function __construct(SubscriptionModel $model, SubscriptionInterface $subscription)
    {
        $this->model        = $model;
        $this->subscription = $subscription;
    }

    /**
     * @return SubscriptionModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param SubscriptionModel $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return SubscriptionInterface
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param SubscriptionInterface $subscription
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
    }
}
