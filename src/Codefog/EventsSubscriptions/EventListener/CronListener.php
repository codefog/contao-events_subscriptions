<?php

namespace Codefog\EventsSubscriptions\EventListener;

use Codefog\EventsSubscriptions\Automator;
use Codefog\EventsSubscriptions\Services;
use Contao\System;

class CronListener
{
    /**
     * @var Automator
     */
    private $automator;

    /**
     * CronListener constructor.
     */
    public function __construct()
    {
        $this->automator = Services::getAutomator();
    }

    /**
     * Execute the tasks on hourly job
     */
    public function onHourlyJob()
    {
        $remindersSent = $this->automator->sendReminders();

        if ($remindersSent > 0) {
            System::log(
                sprintf('A total number of %s event reminders have been sent', $remindersSent),
                __METHOD__,
                TL_CRON
            );
        }
    }
}
