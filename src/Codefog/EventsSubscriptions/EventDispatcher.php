<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions;

use Contao\System;

class EventDispatcher
{
    const EVENT_ON_EXPORT = 'eventsSubscriptions_onExport';
    const EVENT_ON_EXPORT_CALENDAR = 'eventsSubscriptions_onExportCalendar';
    const EVENT_ON_SUBSCRIBE = 'eventsSubscriptions_onSubscribe';
    const EVENT_ON_UNSUBSCRIBE = 'eventsSubscriptions_onUnsubscribe';

    /**
     * Dispatch the event
     *
     * @param string $name
     * @param object $event
     */
    public function dispatch($name, $event)
    {
        if (!is_array($GLOBALS['TL_HOOKS'][$name] ?? null)) {
            return;
        }

        foreach ($GLOBALS['TL_HOOKS'][$name] as $callback) {
            if (is_array($callback)) {
                call_user_func([System::importStatic($callback[0]), $callback[1]], $event);
            }
        }
    }
}
