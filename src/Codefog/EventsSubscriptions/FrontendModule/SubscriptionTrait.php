<?php

namespace Codefog\EventsSubscriptions\FrontendModule;

use Codefog\EventsSubscriptions\EventConfig;
use Contao\Date;

trait SubscriptionTrait
{
    /**
     * Get the subscribe end time
     *
     * @param EventConfig $config
     *
     * @return array
     */
    protected function getSubscribeEndTime(EventConfig $config)
    {
        $endTime = $config->getSubscribeEndTime();

        return [
            'datim'  => Date::parse($GLOBALS['objPage']->datimFormat, $endTime),
            'date'   => Date::parse($GLOBALS['objPage']->dateFormat, $endTime),
            'tstamp' => $endTime,
        ];
    }
}
