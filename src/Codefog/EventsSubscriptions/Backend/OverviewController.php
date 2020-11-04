<?php

namespace Codefog\EventsSubscriptions\Backend;

use Codefog\EventsSubscriptions\EventConfig;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Codefog\EventsSubscriptions\Services;
use Contao\Backend;
use Contao\BackendTemplate;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\Config;
use Contao\Controller;
use Contao\Date;
use Contao\Input;
use Contao\System;

class OverviewController
{
    /**
     * Run the controller
     *
     * @return string
     */
    public function run()
    {
        if (Input::get('key') !== 'subscriptions_overview'
            || ($calendarModel = CalendarModel::findByPk(Input::get('id'))) === null
            || !$calendarModel->subscription_enable
        ) {
            Controller::redirect('contao/main.php?act=error');
        }

        System::loadLanguageFile('tl_calendar');
        System::loadLanguageFile('tl_calendar_events');

        return $this->createTemplate($calendarModel)->parse();
    }

    /**
     * Create the template
     *
     * @param CalendarModel $calendarModel
     *
     * @return BackendTemplate
     */
    protected function createTemplate(CalendarModel $calendarModel)
    {
        $template = new BackendTemplate('be_events_subscriptions_overview');
        $template->backUrl = Backend::getReferer();
        $template->calendar = $calendarModel->title;
        $template->entries = $this->getSubscriptionEntries($calendarModel);

        return $template;
    }

    /**
     * Get the subscription entries
     *
     * @param CalendarModel $calendarModel
     *
     * @return array
     */
    protected function getSubscriptionEntries(CalendarModel $calendarModel)
    {
        $eventModels = CalendarEventsModel::findBy('pid', $calendarModel->id, ['order' => 'startTime DESC']);

        if ($eventModels === null) {
            return [];
        }

        $subscriptionFactory = Services::getSubscriptionFactory();

        /** @var CalendarEventsModel $eventModel */
        foreach ($eventModels as $eventModel) {
            $subscriptionModels = SubscriptionModel::findBy('pid', $eventModel->id, ['order' => 'dateCreated']);

            if ($subscriptionModels === null) {
                continue;
            }

            $groupKey = date('Ym', $eventModel->startTime);

            if (!isset($entries[$groupKey])) {
                $entries[$groupKey] = [
                    'label' => Date::parse('F Y', $eventModel->startTime),
                    'events' => [],
                ];
            }

            $subscriptionsCount = 0;
            $waitingListCount = 0;

            /** @var SubscriptionModel $subscriptionModel */
            foreach ($subscriptionModels as $subscriptionModel) {
                $subscription = $subscriptionFactory->createFromModel($subscriptionModel);

                if ($subscription->isOnWaitingList()) {
                    $waitingListCount += $subscriptionModel->numberOfParticipants;
                } else {
                    $subscriptionsCount += $subscriptionModel->numberOfParticipants;
                }
            }

            $eventConfig = new EventConfig($calendarModel, $eventModel);
            $maximumSubscriptions = $eventConfig->getMaximumSubscriptions();

            $entries[$groupKey]['events'][] = [
                'id' => $eventModel->id,
                'title' => $eventModel->title,
                'date' => Date::parse(Config::get($eventModel->addTime ?'datimFormat' : 'dateFormat'), $eventModel->startTime),
                'subscriptions' => $subscriptionsCount,
                'maxSubscriptions' => $maximumSubscriptions,
                'waitingList' => $eventConfig->hasWaitingList(),
                'waitingListLimit' => $eventConfig->getWaitingListLimit(),
                'waitingListSubscriptions' => ($maximumSubscriptions > 0) ? $waitingListCount : 0,
                'url' => sprintf(
                    'contao?do=calendar&table=tl_calendar_events_subscription&id=%s&rt=%s&ref=%s',
                    $eventModel->id,
                    REQUEST_TOKEN,
                    Input::get('ref')
                ),
                'editUrl' => sprintf(
                    'contao?do=calendar&table=tl_calendar_events&act=edit&id=%s&rt=%s&ref=%s',
                    $eventModel->id,
                    REQUEST_TOKEN,
                    Input::get('ref')
                ),
            ];
        }

        return $entries;
    }
}
