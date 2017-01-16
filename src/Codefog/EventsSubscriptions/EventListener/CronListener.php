<?php

namespace Codefog\EventsSubscriptions\EventListener;

use Codefog\EventsSubscriptions\Automator;
use Contao\System;

class CronListener
{
    /**
     * Execute the tasks on hourly job
     */
    public function onHourlyJob()
    {
        $remindersSent = Automator::sendEmailReminders();

        if ($remindersSent > 0) {
            System::log(
                sprintf('A total number of %s event reminders have been sent', $remindersSent),
                __METHOD__,
                TL_CRON
            );
        }
    }
}
