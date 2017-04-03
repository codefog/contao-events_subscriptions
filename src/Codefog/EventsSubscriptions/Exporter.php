<?php

namespace Codefog\EventsSubscriptions;

use Codefog\EventsSubscriptions\Event\ExportEvent;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Codefog\EventsSubscriptions\Subscription\ExportAwareInterface;
use Codefog\EventsSubscriptions\Subscription\SubscriptionInterface;
use Contao\CalendarEventsModel;
use Contao\Config;
use Contao\Controller;
use Contao\Date;
use Contao\File;
use Contao\Model\Collection;
use Contao\System;
use Haste\IO\Reader\ArrayReader;
use Haste\IO\Writer\CsvFileWriter;

class Exporter
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var SubscriptionFactory
     */
    private $factory;

    /**
     * Exporter constructor.
     *
     * @param EventDispatcher     $eventDispatcher
     * @param SubscriptionFactory $factory
     */
    public function __construct(EventDispatcher $eventDispatcher, SubscriptionFactory $factory)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->factory         = $factory;
    }

    /**
     * Export subscriptions by event
     *
     * @param CalendarEventsModel $event
     *
     * @return File
     *
     * @throws \InvalidArgumentException
     */
    public function exportByEvent(CalendarEventsModel $event)
    {
        return $this->export($event, SubscriptionModel::findBy('pid', $event->id));
    }

    /**
     * Export the subscriptions to a file
     *
     * @param CalendarEventsModel $event
     * @param Collection          $models
     *
     * @return File
     */
    private function export($event, Collection $models = null)
    {
        $subscriptions = [];

        // Create the subscription instances
        if ($models !== null) {
            /** @var SubscriptionModel $model */
            foreach ($models as $model) {
                $subscription = $this->factory->createFromModel($model);

                // Only the export aware subscriptions
                if ($subscription instanceof ExportAwareInterface) {
                    $subscriptions[] = $subscription;
                }
            }
        }

        $reader = $this->getFileReader($event, $subscriptions);
        $writer = $this->getFileWriter();

        // Dispatch the event
        $this->eventDispatcher->dispatch(
            EventDispatcher::EVENT_ON_EXPORT,
            new ExportEvent($reader, $writer, $event, $subscriptions)
        );

        $writer->writeFrom($reader);

        return new File($writer->getFilename(), true);
    }

    /**
     * Get the file reader
     *
     * @param CalendarEventsModel $event
     * @param array               $subscriptions
     *
     * @return ArrayReader
     */
    private function getFileReader(CalendarEventsModel $event, array $subscriptions)
    {
        $columns = $this->getColumns($subscriptions);
        $reader  = new ArrayReader($this->prepareData($event, $subscriptions, $columns));
        $reader->setHeaderFields($columns);

        return $reader;
    }

    /**
     * Get the file writer
     *
     * @return CsvFileWriter
     */
    private function getFileWriter()
    {
        $writer = new CsvFileWriter();
        $writer->enableHeaderFields();

        return $writer;
    }

    /**
     * Get the columns
     *
     * @param array $subscriptions
     *
     * @return array
     */
    private function getColumns(array $subscriptions)
    {
        $headerFields = $GLOBALS['TL_LANG']['MSC']['events_subscriptions.exportHeaderFields'];

        $columns = [
            'event_id'                 => $headerFields['event_id'],
            'event_title'              => $headerFields['event_title'],
            'event_start'              => $headerFields['event_start'],
            'event_end'                => $headerFields['event_end'],
            'subscription_type'        => $headerFields['subscription_type'],
            'subscription_waitingList' => $headerFields['subscription_waitingList'],
            'subscription_firstname'   => $headerFields['subscription_firstname'],
            'subscription_lastname'    => $headerFields['subscription_lastname'],
            'subscription_email'       => $headerFields['subscription_email'],
        ];

        /** @var ExportAwareInterface $subscription */
        foreach ($subscriptions as $subscription) {
            foreach ($subscription->getExportColumns() as $name => $label) {
                if (!array_key_exists($name, $columns)) {
                    $columns[$name] = $label;
                }
            }
        }

        return $columns;
    }

    /**
     * Prepare the data
     *
     * @param CalendarEventsModel $event
     * @param array               $subscriptions
     * @param array               $columns
     *
     * @return array
     */
    private function prepareData(CalendarEventsModel $event, array $subscriptions, array $columns)
    {
        Controller::loadDataContainer('tl_calendar_events');
        System::loadLanguageFile('tl_calendar_events');

        $data      = [];
        $eventData = [
            'event_id'    => $event->id,
            'event_title' => $event->title,
            'event_start' => Date::parse(Config::get('datimFormat'), $event->startTime),
            'event_end'   => Date::parse(Config::get('datimFormat'), $event->endTime),
        ];

        // Get only the columns keys and make sure that values do not contain any data so we don't have
        // the indexes (after flip) as values upon merge later on
        $columns = array_map(
            function () {
                return '';
            },
            array_flip(array_keys($columns))
        );

        /** @var ExportAwareInterface|SubscriptionInterface $subscription */
        foreach ($subscriptions as $subscription) {
            $model = $subscription->getSubscriptionModel();
            $tmp = array_merge(
                $subscription->getExportRow(),
                $eventData,
                [
                    'subscription_type'        => $GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_types']['reference'][$model->type],
                    'subscription_waitingList' => $subscription->isOnWaitingList() ? $GLOBALS['TL_LANG']['MSC']['yes'] : $GLOBALS['TL_LANG']['MSC']['no'],
                ]
            );

            $data[] = array_merge($columns, array_intersect_key($tmp, $columns));
        }

        return $data;
    }
}
