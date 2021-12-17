<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\Event;

use Contao\CalendarModel;
use Haste\IO\Reader\ArrayReader;
use Haste\IO\Writer\AbstractWriter;

class ExportCalendarEvent
{
    /**
     * @var ArrayReader
     */
    private $reader;

    /**
     * @var AbstractWriter
     */
    private $writer;

    /**
     * @var CalendarModel
     */
    private $calendar;

    /**
     * @var array
     */
    private $subscriptions;

    /**
     * @param ArrayReader $reader
     * @param AbstractWriter $writer
     * @param CalendarModel $calendar
     * @param array $subscriptions
     */
    public function __construct(ArrayReader $reader, AbstractWriter $writer, CalendarModel $calendar, array $subscriptions)
    {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->calendar = $calendar;
        $this->subscriptions = $subscriptions;
    }

    /**
     * @return ArrayReader
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * @param ArrayReader $reader
     */
    public function setReader(ArrayReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @return AbstractWriter
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * @param AbstractWriter $writer
     */
    public function setWriter(AbstractWriter $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @return CalendarModel
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param CalendarModel $calendar
     */
    public function setCalendar(CalendarModel $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * @return array
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @param array $subscriptions
     */
    public function setSubscriptions(array $subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }
}
