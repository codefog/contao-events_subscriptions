<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\FrontendModule;

use Codefog\EventsSubscriptions\EventConfig;
use Codefog\EventsSubscriptions\Exception\RedirectException;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Codefog\EventsSubscriptions\Services;
use Contao\Controller;
use Contao\Date;
use Contao\Model\Collection;
use Contao\PageModel;

trait SubscriptionTrait
{
    /**
     * Get the subscription template data
     *
     * @param EventConfig $config
     * @param array       $moduleData
     *
     * @return array
     */
    protected function getSubscriptionTemplateData(EventConfig $config, array $moduleData)
    {
        $data = [
            'subscribeMessage'   => Services::getFlashMessage()->puke($config->getEvent()->id),
            'isEventPast'        => $this->event->startTime < time(),
            'subscribeEndTime'   => $this->getSubscribeEndTime($config),
            'unsubscribeEndTime' => $this->getUnsubscribeEndTime($config),
            'subscribers'        => $this->generateEventSubscribers($config),
            'subscriptionTypes'  => [],
        ];

        $factory = Services::getSubscriptionFactory();

        foreach ($config->getAllowedSubscriptionTypes() as $type) {
            $subscription = $factory->create($type);
            $form         = $subscription->getForm($config);

            if ($form !== null && $form->validate()) {
                try {
                    $subscription->processForm($form, $config);
                } catch (RedirectException $e) {
                    if ($e->getPage() === null) {
                        Controller::reload();
                    } else {
                        $this->handleRedirect($moduleData['jumpTo_'.$e->getPage()], $e->getMessage(), $e->getEventId());
                    }
                }
            }

            $data['subscriptionTypes'][$type] = [
                'form'           => ($form !== null) ? $form->getHelperObject() : null,
                'canSubscribe'   => $subscription->canSubscribe($config),
                'canUnsubscribe' => $subscription->canUnsubscribe($config),
                'isSubscribed'   => $subscription->isSubscribed($config),
            ];
        }

        return $data;
    }

    /**
     * Generate the event subscribers
     *
     * @param EventConfig $config
     *
     * @return array
     */
    protected function generateEventSubscribers(EventConfig $config)
    {
        $subscriptions = SubscriptionModel::findBy('pid', $config->getEvent()->id, ['order' => 'dateCreated']);

        if ($subscriptions === null) {
            return [];
        }

        $factory     = Services::getSubscriptionFactory();
        $subscribers = ['subscribers' => [], 'waitingList' => []];

        /**
         * @var Collection        $subscriptions
         * @var SubscriptionModel $model
         */
        foreach ($subscriptions as $model) {
            $subscription = $factory->createFromModel($model);
            $key          = $subscription->isOnWaitingList() ? 'waitingList' : 'subscribers';

            $subscribers[$key][] = $subscription->getFrontendLabel();
        }

        return $subscribers;
    }

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

    /**
     * Handle the redirect
     *
     * @param int    $pageId
     * @param string $message
     * @param int    $eventId
     */
    private function handleRedirect($pageId, $message, $eventId)
    {
        if (($page = PageModel::findPublishedById($pageId)) !== null) {
            Controller::redirect($page->getFrontendUrl());
        }

        Services::getFlashMessage()->set($message, $eventId);
        Controller::reload();
    }
}
