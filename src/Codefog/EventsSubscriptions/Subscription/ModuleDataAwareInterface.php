<?php

namespace Codefog\EventsSubscriptions\Subscription;

interface ModuleDataAwareInterface
{
    /**
     * @param array $moduleData
     */
    public function setModuleData(array $moduleData);
}
