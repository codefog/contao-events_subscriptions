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
        return $this->getFormattedTimes($config->getSubscribeEndTime());
    }

    /**
     * Get the unsubscribe end time
     *
     * @param EventConfig $config
     *
     * @return array
     */
    protected function getUnsubscribeEndTime(EventConfig $config)
    {
        return $this->getFormattedTimes($config->getUnsubscribeEndTime());
    }

    /**
     * Get the formatted times
     *
     * @param int $time
     *
     * @return array
     */
    private function getFormattedTimes($time)
    {
        return [
            'datim'  => Date::parse($GLOBALS['objPage']->datimFormat, $time),
            'date'   => Date::parse($GLOBALS['objPage']->dateFormat, $time),
            'time'   => Date::parse($GLOBALS['objPage']->timeFormat, $time),
            'tstamp' => $time,
        ];
    }
}
