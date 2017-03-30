<?php

namespace Codefog\EventsSubscriptions;

use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Codefog\EventsSubscriptions\Subscription\SubscriptionInterface;

class SubscriptionFactory
{
    /**
     * Subscription types
     * @var array
     */
    private $types = [];

    /**
     * Add the subscription
     *
     * @param string $type
     * @param string $class
     *
     * @throws \InvalidArgumentException
     */
    public function add($type, $class)
    {
        $ref = new \ReflectionClass($class);

        if (!$ref->implementsInterface('Codefog\EventsSubscriptions\Subscription\SubscriptionInterface')) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The class "%s" must be an instance of \Codefog\EventsSubscriptions\Subscription\SubscriptionInterface',
                    $class
                )
            );
        }

        $this->types[$type] = $class;
    }

    /**
     * Get the subscription class
     *
     * @param string $type
     *
     * @return SubscriptionInterface
     *
     * @throws \InvalidArgumentException
     */
    public function create($type)
    {
        if (!array_key_exists($type, $this->types)) {
            throw new \InvalidArgumentException(sprintf('The subscription type "%s" does not exist', $type));
        }

        return new $this->types[$type];
    }

    /**
     * Create the subscription from model
     *
     * @param SubscriptionModel $model
     *
     * @return SubscriptionInterface
     */
    public function createFromModel(SubscriptionModel $model)
    {
        $subscription = $this->create($model->type);
        $subscription->setSubscriptionModel($model);

        return $subscription;
    }

    /**
     * Get the type by class name
     *
     * @param string $class
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getType($class)
    {
        if (($type = array_search($class, $this->types, true)) === false) {
            throw new \InvalidArgumentException(sprintf('The subscription class "%s" does not exist', $class));
        }

        return $type;
    }

    /**
     * Get all types
     *
     * @return array
     */
    public function getAll()
    {
        return array_keys($this->types);
    }
}
