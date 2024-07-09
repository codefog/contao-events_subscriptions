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

use Contao\CalendarEventsModel;
use Contao\File;

class ExportEvent
{
    public function __construct(
        private File $file,
        private CalendarEventsModel $event,
        private array $subscriptions
    ) {
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    /**
     * @return CalendarEventsModel
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param CalendarEventsModel $event
     */
    public function setEvent(CalendarEventsModel $event)
    {
        $this->event = $event;
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
