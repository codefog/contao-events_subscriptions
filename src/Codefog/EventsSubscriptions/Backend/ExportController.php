<?php

namespace Codefog\EventsSubscriptions\Backend;

use Codefog\EventsSubscriptions\Exporter;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Codefog\EventsSubscriptions\Services;
use Contao\Backend;
use Contao\BackendTemplate;
use Contao\CalendarEventsModel;
use Contao\Controller;
use Contao\Date;
use Contao\Environment;
use Contao\Input;
use Contao\System;
use Haste\Util\Format;

class ExportController
{
    /**
     * @var Exporter
     */
    protected $exporter;

    /**
     * ExportController constructor.
     */
    public function __construct()
    {
        $this->exporter = Services::getExporter();
    }

    /**
     * Run the controller
     *
     * @return string
     */
    public function run()
    {
        if (Input::get('key') !== 'subscriptions_export'
            || ($eventModel = CalendarEventsModel::findByPk(Input::get('id'))) === null
        ) {
            Controller::redirect('contao/main.php?act=error');
        }

        System::loadLanguageFile('tl_calendar_events_subscription');

        $formSubmit = 'events-subscriptions-export';

        // Process the form
        if (Input::post('FORM_SUBMIT') === $formSubmit) {
            $this->processForm($eventModel);
        }

        return $this->createTemplate($eventModel, $formSubmit)->parse();
    }

    /**
     * Create the template
     *
     * @param CalendarEventsModel $eventModel
     * @param string              $formSubmit
     *
     * @return BackendTemplate
     */
    protected function createTemplate(CalendarEventsModel $eventModel, $formSubmit)
    {
        $eventData = [];

        // Format the event data
        foreach ($eventModel->row() as $k => $v) {
            $eventData[$k] = Format::dcaValue($eventModel::getTable(), $k, $v);;
        }

        $template = new BackendTemplate('be_events_subscriptions_export');
        $template->backUrl = Backend::getReferer();
        $template->event = $eventData;
        $template->eventRaw = $eventModel->row();
        $template->subscriptionsCount = SubscriptionModel::countBy('pid', $eventModel->id);
        $template->action = Environment::get('request');
        $template->formSubmit = $formSubmit;
        $template->excelFormatSupport = $this->exporter->isFormatSupported(Exporter::FORMAT_EXCEL);

        return $template;
    }

    /**
     * Process the form
     *
     * @param CalendarEventsModel $eventModel
     */
    protected function processForm(CalendarEventsModel $eventModel)
    {
        if (isset($_POST['export_excel'])) {
            $format = Exporter::FORMAT_EXCEL;
        } elseif (isset($_POST['export_csv'])) {
            $format = Exporter::FORMAT_CSV;
        } else {
            Controller::reload();
        }

        $file = $this->exporter->exportByEvent($eventModel, $format);

        $fileName = sprintf(
            '%s_%s.%s',
            standardize($eventModel->title),
            standardize(Date::parse('Y-m-d', $eventModel->startDate)),
            $file->extension
        );

        $file->sendToBrowser($fileName);
    }
}
