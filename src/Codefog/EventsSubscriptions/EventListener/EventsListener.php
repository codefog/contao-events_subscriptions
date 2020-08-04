<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\EventListener;

use Codefog\EventsSubscriptions\EventConfig;
use Codefog\EventsSubscriptions\Services;

class EventsListener
{
    /**
     * On get all events
     *
     * @param array $allEvents
     *
     * @return array
     */
    public function onGetAllEvents(array $allEvents)
    {
        $factory = Services::getSubscriptionFactory();

        foreach ($allEvents as $k => $periods) {
            foreach ($periods as $kk => $events) {
                foreach ($events as $kkk => $event) {
                    $config = EventConfig::create($event['id']);

                    foreach ($config->getAllowedSubscriptionTypes() as $type) {
                        if ($factory->create($type)->isSubscribed($config)) {
                            $allEvents[$k][$kk][$kkk]['class'] = rtrim($event['class']) . ' subscribed';
                        }
                    }
                }
            }
        }

        return $allEvents;
    }
}
