<?php

namespace Codefog\EventsSubscriptions\Backend;

use Codefog\EventsSubscriptions\Services;
use Contao\CalendarEventsModel;
use Contao\Config;
use Contao\Controller;
use Contao\Date;
use Contao\Input;

class ExportController
{
    /**
     * Run the controller
     */
    public function run()
    {
        if (Input::get('key') !== 'subscriptions_export'
            || ($event = CalendarEventsModel::findByPk(Input::get('id'))) === null
        ) {
            Controller::redirect('contao/main.php?act=error');
        }

        $file = Services::getExporter()->exportByEvent($event);

        $fileName = sprintf(
            '%s_%s.%s',
            standardize($event->title),
            standardize(Date::parse(Config::get('dateFormat'), $event->startDate)),
            $file->extension
        );

        $file->sendToBrowser($fileName);
    }
}
