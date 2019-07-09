<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\EventListener;

use Contao\Database;

class TableListener
{
    /**
     * On revise table records
     *
     * @param string $table
     * @param array  $records
     */
    public function onReviseTable($table, $records)
    {
        if ($table === 'tl_calendar_events_subscription') {
            Database::getInstance()
                ->prepare("DELETE FROM tl_calendar_events_subscription WHERE (type=? AND firstname='' AND lastname='' AND email='') OR (type=? AND member=0)")
                ->execute('guest', 'member')
            ;
        }
    }
}
