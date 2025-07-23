<?php

namespace Codefog\EventsSubscriptionsBundle\Subscription;

interface ModuleDataAwareInterface
{
    /**
     * @param array $moduleData
     */
    public function setModuleData(array $moduleData);
}
