<?php

namespace Codefog\EventsSubscriptions\Backend;

use Codefog\EventsSubscriptions\Exporter;
use Codefog\EventsSubscriptions\Services;
use Codefog\HasteBundle\Formatter;
use Contao\Backend;
use Contao\BackendTemplate;
use Contao\CalendarModel;
use Contao\Config;
use Contao\Controller;
use Contao\Date;
use Contao\Environment;
use Contao\Input;
use Contao\System;
use Contao\Widget;

class ExportCalendarController
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
        if (Input::get('key') !== 'subscriptions_exportCalendar'
            || ($calendarModel = CalendarModel::findByPk(Input::get('id'))) === null
        ) {
            Controller::redirect('contao/main.php?act=error');
        }

        System::loadLanguageFile('tl_calendar');

        /** @var Widget $startDateWidget */
        $startDateWidget = new $GLOBALS['BE_FFL']['text'](Widget::getAttributesFromDca([
            'label' => &$GLOBALS['TL_LANG']['tl_calendar']['export.startDate'],
            'eval' => ['rgxp' => 'date'],
        ], 'startDate'));

        /** @var Widget $endDateWidget */
        $endDateWidget = new $GLOBALS['BE_FFL']['text'](Widget::getAttributesFromDca([
            'label' => &$GLOBALS['TL_LANG']['tl_calendar']['export.endDate'],
            'eval' => ['rgxp' => 'date'],
        ], 'endDate'));

        $formSubmit = 'events-subscriptions-calendar-export';

        // Process the form
        if (Input::post('FORM_SUBMIT') === $formSubmit) {
            $startDateWidget->validate();
            $endDateWidget->validate();

            if (!$startDateWidget->hasErrors() && !$endDateWidget->hasErrors()) {
                $this->processForm($calendarModel, $startDateWidget, $endDateWidget);
            }
        }

        return $this->createTemplate($calendarModel, $startDateWidget, $endDateWidget, $formSubmit)->parse();
    }

    /**
     * Create the template
     */
    protected function createTemplate(CalendarModel $calendarModel, Widget $startDateWidget, Widget $endDateWidget, $formSubmit)
    {
        $calendarData = [];

        // Format the event data
        foreach ($calendarModel->row() as $k => $v) {
            $calendarData[$k] = System::getContainer()->get(Formatter::class)->dcaValue($calendarModel::getTable(), $k, $v);
        }

        $template = new BackendTemplate('be_events_subscriptions_export_calendar');
        $template->backUrl = Backend::getReferer();
        $template->calendar = $calendarData;
        $template->action = Environment::get('request');
        $template->formSubmit = $formSubmit;
        $template->excelFormatSupport = $this->exporter->isFormatSupported(Exporter::FORMAT_EXCEL);
        $template->startDate = $startDateWidget;
        $template->endDate = $endDateWidget;
        $template->datePickerFormat = Date::formatToJs(Config::get('dateFormat'));

        return $template;
    }

    /**
     * Process the form
     */
    protected function processForm(CalendarModel $calendarModel, Widget $startDateWidget, Widget $endDateWidget)
    {
        if (isset($_POST['export_excel'])) {
            $format = Exporter::FORMAT_EXCEL;
        } elseif (isset($_POST['export_csv'])) {
            $format = Exporter::FORMAT_CSV;
        } else {
            Controller::reload();
        }

        $startTstamp = null;
        $endTstamp = null;

        // Determine the start date
        if ($startDateWidget->value) {
            try {
                $startTstamp = (new Date($startDateWidget->value, Date::getNumericDateFormat()))->tstamp;
            } catch (\OutOfBoundsException $e) {}
        }

        // Determine the end date
        if ($endDateWidget->value) {
            try {
                $endTstamp = (new Date($endDateWidget->value, Date::getNumericDateFormat()))->tstamp;
            } catch (\OutOfBoundsException $e) {}
        }

        $file = $this->exporter->exportByCalendar($calendarModel, $startTstamp, $endTstamp, $format);

        $fileName = sprintf(
            '%s_%s.%s',
            standardize($calendarModel->title),
            standardize(Date::parse('Y-m-d')),
            $file->extension
        );

        $file->sendToBrowser($fileName);
    }
}
