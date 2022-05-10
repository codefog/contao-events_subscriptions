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
use Codefog\EventsSubscriptions\FrontendModule\SubscriptionTrait;
use Codefog\EventsSubscriptions\Services;
use Contao\Module;

class EventsListener
{
    use SubscriptionTrait;

    /**
     * On get all events
     */
    public function onGetAllEvents(array $allEvents, array $calendars, $start, $end, Module $module)
    {
        $factory = Services::getSubscriptionFactory();

        foreach ($allEvents as $k => $periods) {
            foreach ($periods as $kk => $events) {
                foreach ($events as $kkk => $event) {
                    if (!isset($event['id'])) {
                        continue;
                    }

                    $config = EventConfig::create($event['id']);

                    // Set the subscription template data for calendar module
                    if ($module->type === 'calendar') {
                        foreach ($this->getSubscriptionTemplateData($config, $module->getModel()->row()) as $field => $value) {
                            $allEvents[$k][$kk][$kkk][$field] = $value;
                        }
                    }

                    // Add extra CSS classes
                    foreach ($config->getAllowedSubscriptionTypes() as $type) {
                        try {
                            $subscription = $factory->create($type);
                        } catch (\InvalidArgumentException $e) {
                            continue;
                        }

                        // Add CSS class if user is subscribed
                        if ($subscription->isSubscribed($config)) {
                            $class = 'subscribed';

                            if ($subscription->isOnWaitingList()) {
                                $class .= ' subscribed-waiting-list';
                            }

                            $allEvents[$k][$kk][$kkk]['class'] = rtrim($event['class']) . ' ' . $class;
                        }

                        // Add CSS class if user can subscribe
                        if ($subscription->canSubscribe($config)) {
                            $allEvents[$k][$kk][$kkk]['class'] = rtrim($event['class']) . ' can-subscribe';
                        }
                    }
                }
            }
        }

        return $allEvents;
    }
}
