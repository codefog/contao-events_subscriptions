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
use Codefog\EventsSubscriptions\Subscription\MemberSubscription;
use Contao\Controller;
use Contao\Date;
use Contao\FrontendUser;
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
            'subscriptionWaitingList'      => $config->hasWaitingList(),
            'subscriptionWaitingListLimit' => $config->getWaitingListLimit(),
            'isEventPast'        => $config->getEvent()->startTime < time(),
            'subscribeEndTime'   => $this->getSubscribeEndTime($config),
            'unsubscribeEndTime' => $this->getUnsubscribeEndTime($config),
            'subscribers'        => $this->generateEventSubscribers($config),
            'subscriptionMaximum' => $config->getMaximumSubscriptions(),
            'subscriptionTypes'  => [],
        ];

        // Add a helper variable that indicates subscription to a waiting list (see #32)
        $data['subscribeWaitingList'] = $config->hasWaitingList() && ($data['subscriptionMaximum'] > 0) && ($data['subscriptionMaximum'] - count($data['subscribers']['subscribers']) <= 0);

        $factory = Services::getSubscriptionFactory();

        foreach ($config->getAllowedSubscriptionTypes() as $type) {
            $subscription = $factory->create($type);
            $form         = $subscription->getForm($config);

            if ($form !== null && $form->validate()) {
                try {
                    $subscription->processForm($form, $config);
                } catch (RedirectException $e) {
                    if ($e->getPage() === null) {
                        // Set a flash message, if any
                        if ($e->getMessage() && $e->getEventId()) {
                            Services::getFlashMessage()->set($e->getMessage(), $e->getEventId());
                        }

                        Controller::reload();
                    } else {
                        $this->handleRedirect($moduleData['jumpTo_'.$e->getPage()], $e->getMessage(), $e->getEventId());
                    }
                }

                // If the form was submitted but no redirection was made and the appropriate field is there,
                // it means we can subscribe to the waiting list
                if ($form->hasFormField('waitingList')) {
                    $data['subscribeWaitingList'] = true;
                }
            }

            $data['subscriptionTypes'][$type] = [
                'form'           => ($form !== null) ? $form->getHelperObject() : null,
                'canSubscribe'   => $subscription->canSubscribe($config),
                'canUnsubscribe' => $subscription->canUnsubscribe($config),
                'isSubscribed'   => $subscription->isSubscribed($config),
                'isOnWaitingList' => false,
            ];

            // Check if member is on waiting list
            if ($data['subscriptionTypes'][$type]['isSubscribed'] && $subscription instanceof MemberSubscription) {
                $subscription->setSubscriptionModel(SubscriptionModel::findOneBy(['pid=? AND member=?'], [$config->getEvent()->id, FrontendUser::getInstance()->id]));
                $data['subscriptionTypes'][$type]['isOnWaitingList'] = $subscription->isOnWaitingList();
            }
        }

        // Add the flash messages after the forms are processed
        $data['subscribeMessage'] = Services::getFlashMessage()->puke($config->getEvent()->id);

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
        $subscribers = ['subscribers' => [], 'waitingList' => []];
        $subscriptions = SubscriptionModel::findBy('pid', $config->getEvent()->id, ['order' => 'dateCreated']);

        if ($subscriptions === null) {
            return $subscribers;
        }

        $factory     = Services::getSubscriptionFactory();

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
