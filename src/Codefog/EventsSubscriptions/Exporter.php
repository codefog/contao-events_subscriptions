<?php

namespace Codefog\EventsSubscriptions;

use Codefog\EventsSubscriptions\Event\ExportCalendarEvent;
use Codefog\EventsSubscriptions\Event\ExportEvent;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Codefog\EventsSubscriptions\Subscription\ExportAwareInterface;
use Codefog\EventsSubscriptions\Subscription\SubscriptionInterface;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\Config;
use Contao\Controller;
use Contao\Date;
use Contao\File;
use Contao\System;
use League\Csv\Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Filesystem\Path;

class Exporter
{
    const FORMAT_CSV = 'csv';
    const FORMAT_EXCEL = 'excel';

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
     * @param CalendarEventsModel $eventModel
     * @param string              $format
     *
     * @return File
     *
     * @throws \InvalidArgumentException
     */
    public function exportByEvent(CalendarEventsModel $eventModel, $format = self::FORMAT_CSV)
    {
        $subscriptions = [];

        // Create the subscription instances
        if (($subscriptionModels = SubscriptionModel::findBy('pid', $eventModel->id)) !== null) {
            /** @var SubscriptionModel $subscriptionModel */
            foreach ($subscriptionModels as $subscriptionModel) {
                try {
                    $subscription = $this->factory->createFromModel($subscriptionModel);
                } catch (\InvalidArgumentException $e) {
                    continue;
                }

                // Only the export aware subscriptions
                if ($subscription instanceof ExportAwareInterface) {
                    $subscriptions[] = $subscription;
                }
            }
        }

        $data = $this->prepareData($eventModel, $subscriptions, $this->getColumns($subscriptions));

        $file = match ($format) {
            self::FORMAT_CSV => $this->exportToCsvFile($data),
            self::FORMAT_EXCEL => $this->exportToExcelFile($data),
            default => throw new \InvalidArgumentException(sprintf('Invalid export format: %s', $format)),
        };

        // Dispatch the event
        $this->eventDispatcher->dispatch(
            EventDispatcher::EVENT_ON_EXPORT,
            $event = new ExportEvent($file, $eventModel, $subscriptions)
        );

        return $event->getFile();
    }

    /**
     * Export subscriptions by calendar
     *
     * @return File
     *
     * @throws \InvalidArgumentException
     */
    public function exportByCalendar(CalendarModel $calendar, $startTstamp = null, $endTstamp = null, $format = self::FORMAT_CSV)
    {
        $data = [];
        $subscriptions = [];

        // Get the reader data
        if (($events = CalendarEventsModel::findBy('pid', $calendar->id)) !== null) {
            $columns = ['pid IN (' . implode(', ', $events->fetchEach('id')) . ')'];
            $values = [];

            if ($startTstamp !== null) {
                $columns[] = 'dateCreated>=?';
                $values[] = $startTstamp;
            }

            if ($endTstamp !== null) {
                $columns[] = 'dateCreated<=?';
                $values[] = $endTstamp;
            }

            // Create the subscription instances
            if (($subscriptionModels = SubscriptionModel::findBy($columns, $values)) !== null) {
                /** @var SubscriptionModel $subscriptionModel */
                foreach ($subscriptionModels as $subscriptionModel) {
                    try {
                        $subscription = $this->factory->createFromModel($subscriptionModel);
                    } catch (\InvalidArgumentException $e) {
                        continue;
                    }

                    // Only the export aware subscriptions
                    if ($subscription instanceof ExportAwareInterface) {
                        $subscriptions[] = $subscription;
                    }
                }
            }

            $columns = $this->getColumns($subscriptions);

            /** @var CalendarEventsModel $event */
            foreach ($events as $event) {
                $eventSubscriptions = [];

                // Get only the subscriptions for the currently iterated event
                foreach ($subscriptions as $subscription) {
                    if ((int) $event->id === (int) $subscription->getSubscriptionModel()->pid) {
                        $eventSubscriptions[] = $subscription;
                    }
                }

                if (count($eventSubscriptions) === 0) {
                    continue;
                }

                $data = array_merge($data, $this->prepareData($event, $eventSubscriptions, $columns));
            }
        }

        $file = match ($format) {
            self::FORMAT_CSV => $this->exportToCsvFile($data),
            self::FORMAT_EXCEL => $this->exportToExcelFile($data),
            default => throw new \InvalidArgumentException(sprintf('Invalid export format: %s', $format)),
        };

        // Dispatch the event
        $this->eventDispatcher->dispatch(
            EventDispatcher::EVENT_ON_EXPORT_CALENDAR,
            $event = new ExportCalendarEvent($file, $calendar, $subscriptions)
        );

        return $event->getFile();
    }

    private function exportToCsvFile(array $data): File
    {
        $projectDir = System::getContainer()->getParameter('kernel.project_dir');
        $tmpFile = tempnam(Path::join($projectDir, 'system/tmp'), 'es-') . '.csv';

        $writer = Writer::createFromString();
        $writer->insertOne(array_keys($data[0]));
        $writer->insertAll($data);

        file_put_contents($tmpFile, $writer->toString());

        return new File(Path::makeRelative($tmpFile, $projectDir));
    }

    private function exportToExcelFile(array $data): File
    {
        $projectDir = System::getContainer()->getParameter('kernel.project_dir');
        $tmpFile = tempnam(Path::join($projectDir, 'system/tmp'), 'es-') . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers to the first row
        foreach (array_keys($data[0]) as $index => $header) {
            $sheet->setCellValue([$index + 1, 1], $header);
        }

        $rowIndex = 2;

        // Add data to the spreadsheet
        foreach ($data as $row) {
            $columnIndex = 1;

            foreach ($row as $value) {
                $sheet->setCellValue([$columnIndex, $rowIndex], $value);
                $columnIndex++;
            }

            $rowIndex++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpFile);

        return new File(Path::makeRelative($tmpFile, $projectDir));
    }

    /**
     * Return true if the Excel format is supported
     *
     * @param string $format
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public function isFormatSupported($format)
    {
        switch ($format) {
            case self::FORMAT_CSV:
            case self::FORMAT_EXCEL:
                return true;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid export format: %s', $format));
        }
    }

    /**
     * Get the file writer
     *
     * @param string $format
     *
     * @return CsvFileWriter
     *
     * @throws \InvalidArgumentException
     */
    private function getFileWriter($format)
    {
        switch ($format) {
            case self::FORMAT_CSV:
                $writer = new CsvFileWriter();
                break;
            case self::FORMAT_EXCEL:
                $writer = new ExcelFileWriter();
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid export format: %s', $format));
        }

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
            'subscription_date'        => $headerFields['subscription_date'],
            'subscription_type'        => $headerFields['subscription_type'],
            'subscription_waitingList' => $headerFields['subscription_waitingList'],
            'subscription_firstname'   => $headerFields['subscription_firstname'],
            'subscription_lastname'    => $headerFields['subscription_lastname'],
            'subscription_email'       => $headerFields['subscription_email'],
            'subscription_numberOfParticipants' => $headerFields['subscription_numberOfParticipants'],
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
                    'subscription_date'        => Date::parse(Config::get('datimFormat'), $model->dateCreated),
                    'subscription_type'        => $GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_types']['reference'][$model->type],
                    'subscription_waitingList' => $subscription->isOnWaitingList() ? $GLOBALS['TL_LANG']['MSC']['yes'] : $GLOBALS['TL_LANG']['MSC']['no'],
                ]
            );

            $data[] = array_merge($columns, array_intersect_key($tmp, $columns));
        }

        return $data;
    }
}
