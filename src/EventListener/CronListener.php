<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle\EventListener;

use Codefog\EventsSubscriptionsBundle\Automator;
use Codefog\EventsSubscriptionsBundle\Services;
use Contao\CoreBundle\Monolog\ContaoContext;
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
            System::getContainer()->get('monolog.logger.contao')->log(
                sprintf('A total number of %s event reminders have been sent', $remindersSent),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::CRON)],
            );
        }
    }
}
