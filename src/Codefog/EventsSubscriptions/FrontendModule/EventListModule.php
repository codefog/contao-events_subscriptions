<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\FrontendModule;

use Codefog\EventsSubscriptions\EventConfig;
use Contao\Date;
use Contao\Pagination;

class EventListModule extends \Events
{
    use SubscriptionTrait;

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_event_list_subscribe';

    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.utf8_strtoupper(
                    $GLOBALS['TL_LANG']['FMD']['event_list_subscribe'][0]
                ).' ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        $this->cal_calendar = $this->sortOutProtected(deserialize($this->cal_calendar, true));

        // Return if there are no calendars
        if (!is_array($this->cal_calendar) || empty($this->cal_calendar)) {
            return '';
        }

        return parent::generate();
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        list($strBegin, $strEnd, $strEmpty) = $this->getDatesFromFormat(new \Date(), 'next_all');
        $arrFutureEvents         = $this->getAllEvents($this->cal_calendar, $strBegin, $strEnd);
        $this->Template->message = '';

        // Return if there are no events
        if (empty($arrFutureEvents)) {
            $this->Template->message = $strEmpty;

            return;
        }

        $sort = ($this->cal_order == 'descending') ? 'krsort' : 'ksort';
        $sort($arrFutureEvents);

        // Sort the events
        foreach (array_keys($arrFutureEvents) as $key) {
            $sort($arrFutureEvents[$key]);
        }

        $arrAllEvents = array();

        // Remove events outside the scope
        foreach ($arrFutureEvents as $days) {
            foreach ($days as $day => $events) {
                foreach ($events as $event) {
                    $event['firstDay']  = $GLOBALS['TL_LANG']['DAYS'][date('w', $day)];
                    $event['firstDate'] = Date::parse($GLOBALS['objPage']->dateFormat, $day);
                    $event['datetime']  = date('Y-m-d', $day);

                    $arrAllEvents[$event['id']] = $event;
                }
            }
        }

        unset($arrFutureEvents);
        $arrAllEvents = array_values($arrAllEvents);
        $total        = count($arrAllEvents);
        $limit        = $total;
        $offset       = 0;

        // Overall limit
        if ($this->cal_limit > 0) {
            $total = min($this->cal_limit, $total);
            $limit = $total;
        }

        // Pagination
        if ($this->perPage > 0) {
            $id   = 'page_e'.$this->id;
            $page = \Input::get('page') ?: 1;

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total / $this->perPage), 1)) {
                global $objPage;
                $objPage->noSearch = 1;
                $objPage->cache    = 0;

                // Send a 404 header
                header('HTTP/1.1 404 Not Found');

                return;
            }

            $offset = ($page - 1) * $this->perPage;
            $limit  = min($this->perPage + $offset, $total);

            $objPagination              = new Pagination($total, $this->perPage, 7, $id);
            $this->Template->pagination = $objPagination->generate("\n  ");
        }

        $arrEvents = array();
        $intEvents = 0;
        $imgSize   = false;

        // Override the default image size
        if ($this->imgSize != '') {
            $size = deserialize($this->imgSize);

            if ($size[0] > 0 || $size[1] > 0) {
                $imgSize = $this->imgSize;
            }
        }

        // Parse events
        for ($i = $offset; $i < $limit; $i++) {
            $arrEvent    = $arrAllEvents[$i];
            $objTemplate = new \FrontendTemplate($this->cal_template);
            $objTemplate->setData($arrEvent);

            $subscriptionConfig = EventConfig::create($arrEvent['id']);
            $subscriptionData   = $this->getSubscriptionBasicData($subscriptionConfig);

            // Add the subscription form
            if ($subscriptionData['canSubscribe'] || $subscriptionData['canUnsubscribe']) {
                $form = $this->createSubscriptionForm('event-subscription-'.$arrEvent['id']);

                if ($form->validate()) {
                    $this->processSubscriptionForm($subscriptionConfig, $this->arrData);
                }

                $objTemplate->subscriptionForm = $form->getHelperObject();
            }

            // Add the subscription data to the template
            foreach ($subscriptionData as $k => $v) {
                $objTemplate->$k = $v;
            }

            $objTemplate->classList   = $arrEvent['class'].((($intEvents % 2) == 0) ? ' even' : ' odd').(($intEvents == 0) ? ' first' : '').((++$intEvents == $limit) ? ' last' : '').' cal_'.$arrEvent['parent'];
            $objTemplate->readMore    = specialchars(
                sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $arrEvent['title'])
            );
            $objTemplate->more        = $GLOBALS['TL_LANG']['MSC']['more'];
            $objTemplate->startTstamp = $arrEvent['startTime'];
            $objTemplate->endTstamp   = $arrEvent['endTime'];
            $objTemplate->startTime   = Date::parse($GLOBALS['objPage']->timeFormat, $arrEvent['startTime']);
            $objTemplate->startDate   = Date::parse($GLOBALS['objPage']->dateFormat, $arrEvent['startDate']);
            $objTemplate->endTime     = Date::parse($GLOBALS['objPage']->timeFormat, $arrEvent['endTime']);
            $objTemplate->endDate     = Date::parse($GLOBALS['objPage']->dateFormat, $arrEvent['endDate']);
            $objTemplate->addImage    = false;

            // Add image
            if ($arrEvent['addImage'] && is_file(TL_ROOT.'/'.$arrEvent['singleSRC'])) {
                if ($imgSize) {
                    $arrEvent['size'] = $imgSize;
                }

                static::addImageToTemplate($objTemplate, $arrEvent);
                $objTemplate->href = $arrEvent['href'];
            }

            $objTemplate->enclosure = array();

            // Add enclosure
            if ($arrEvent['addEnclosure']) {
                static::addEnclosuresToTemplate($objTemplate, $arrEvent);
            }

            $arrEvents[] = $objTemplate->parse();
        }

        $this->Template->events = $arrEvents;
    }
}
