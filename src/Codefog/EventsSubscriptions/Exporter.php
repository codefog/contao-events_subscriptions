<?php

namespace Codefog\EventsSubscriptions;

use Codefog\EventsSubscriptions\Event\ExportEvent;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Contao\CalendarEventsModel;
use Contao\Config;
use Contao\Date;
use Contao\File;
use Contao\Model\Collection;
use Haste\IO\Reader\ArrayReader;
use Haste\IO\Writer\CsvFileWriter;

class Exporter
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Exporter constructor.
     *
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
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
        return $this->export(SubscriptionModel::findBy('pid', $event->id));
    }

    /**
     * Export the subscriptions to a file
     *
     * @param Collection $subscriptions
     *
     * @return File
     */
    private function export(Collection $subscriptions = null)
    {
        $data   = ($subscriptions !== null) ? $this->prepareData($subscriptions) : [];
        $reader = $this->getFileReader($data);
        $writer = $this->getFileWriter();

        // Dispatch the event
        $this->eventDispatcher->dispatch(
            EventDispatcher::EVENT_ON_EXPORT,
            new ExportEvent($reader, $writer, $subscriptions)
        );

        $writer->writeFrom($reader);

        return new File($writer->getFilename(), true);
    }

    /**
     * Get the file reader
     *
     * @param array $data
     *
     * @return ArrayReader
     */
    private function getFileReader(array $data)
    {
        $reader = new ArrayReader($data);
        $reader->setHeaderFields($GLOBALS['TL_LANG']['ERR']['events_subscriptions.exportHeaderFields']);

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
     * Prepare the data
     *
     * @param Collection $subscriptions
     *
     * @return array
     */
    private function prepareData(Collection $subscriptions)
    {
        $data = [];

        /** @var SubscriptionModel $subscription */
        foreach ($subscriptions as $subscription) {
            $event  = $subscription->getEvent();
            $member = $subscription->getMember();

            $data[] = [
                $event->id,
                $event->title,
                Date::parse(Config::get('datimFormat'), $event->startTime),
                Date::parse(Config::get('datimFormat'), $event->endTime),
                $member->id,
                $member->firstname,
                $member->lastname,
                $member->email,
                $member->username,
            ];
        }

        return $data;
    }
}
