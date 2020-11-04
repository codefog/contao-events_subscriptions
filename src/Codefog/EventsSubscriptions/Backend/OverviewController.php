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
use Contao\Database;
use Contao\Date;
use Contao\Input;
use Contao\Model\Collection;
use Contao\Pagination;
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
        $total = (int) Database::getInstance()
            ->prepare('SELECT COUNT(*) AS total FROM tl_calendar_events WHERE pid=? AND EXISTS(SELECT 1 FROM tl_calendar_events_subscription WHERE tl_calendar_events_subscription.pid=tl_calendar_events.id)')
            ->execute($calendarModel->id)
            ->total;

        $limit = Config::get('maxResultsPerPage');
        $page = (Input::get('esp') ?: 1) - 1;
        $pagination = new Pagination($total, $limit, 7, 'esp', new BackendTemplate('be_pagination'), true);

        $template = new BackendTemplate('be_events_subscriptions_overview');
        $template->backUrl = Backend::getReferer();
        $template->calendar = $calendarModel->title;
        $template->entries = $this->getSubscriptionEntries($calendarModel, $limit, $page * $limit);
        $template->pagination = $pagination->generate();

        return $template;
    }


    /**
     * Get the subscription entries
     *
     * @param CalendarModel $calendarModel
     * @param int           $limit
     * @param int           $offset
     *
     * @return array
     */
    protected function getSubscriptionEntries(CalendarModel $calendarModel, $limit, $offset)
    {
        $events = Database::getInstance()
            ->prepare('SELECT * FROM tl_calendar_events WHERE pid=? AND EXISTS(SELECT 1 FROM tl_calendar_events_subscription WHERE tl_calendar_events_subscription.pid=tl_calendar_events.id) ORDER BY startTime DESC')
            ->limit($limit, $offset)
            ->execute($calendarModel->id);

        if (!$events->numRows) {
            return [];
        }

        $subscriptionFactory = Services::getSubscriptionFactory();

        /** @var CalendarEventsModel $eventModel */
        foreach (Collection::createFromDbResult($events, 'tl_calendar_events') as $eventModel) {
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
                'notificationsUrl' => sprintf(
                    'contao?do=calendar&table=tl_calendar_events&key=subscriptions_notification&id=%s&rt=%s&ref=%s',
                    $eventModel->id,
                    REQUEST_TOKEN,
                    Input::get('ref')
                ),
            ];
        }

        return $entries;
    }
}
