<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * Copyright (C) 2013 Codefog
 *
 * @package events_subscriptions
 * @author  Codefog <http://codefog.pl>
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */

namespace Codefog\EventsSubscriptions\FrontendModule;

use Contao\Date;
use Contao\Environment;
use Codefog\EventsSubscriptions\EventsSubscriptions;
use Contao\FrontendUser;
use Contao\Pagination;

class EventListModule extends \Events
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_eventlistsubscribe';


    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### EVENT LIST SUBSCRIBE ###';
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
            $arrEvent     = $arrAllEvents[$i];
            $user         = FrontendUser::getInstance();
            $canSubscribe = EventsSubscriptions::canSubscribe($arrEvent['id'], $user->id);
            $isSubscribed = EventsSubscriptions::isSubscribed($arrEvent['id'], $user->id);
            $strFormId    = 'event_subscribe_'.$this->id.'_'.$arrEvent['id'];

            // Process the form
            if (($canSubscribe || $isSubscribed) && \Input::post('FORM_SUBMIT') == $strFormId) {
                if (!FE_USER_LOGGED_IN) {
                    $this->jumpToOrReload($this->jumpTo_login);
                }

                // Subscribe user
                if ($canSubscribe) {
                    if (EventsSubscriptions::subscribeMember($arrEvent['id'], $user->id)) {
                        if (!$this->jumpTo_subscribe) {
                            $_SESSION['EVENT_SUBSCRIBE_MESSAGE'][$arrEvent['id']] = $GLOBALS['TL_LANG']['MSC']['eventSubscribed'];
                        }

                        $this->jumpToOrReload($this->jumpTo_subscribe);
                    }
                } // Unsubscribe user
                else {
                    if (EventsSubscriptions::unsubscribeMember($arrEvent['id'], $user->id)) {
                        if (!$this->jumpTo_unsubscribe) {
                            $_SESSION['EVENT_SUBSCRIBE_MESSAGE'][$arrEvent['id']] = $GLOBALS['TL_LANG']['MSC']['eventUnsubscribed'];
                        }

                        $this->jumpToOrReload($this->jumpTo_unsubscribe);
                    }
                }
            }

            $objTemplate = new \FrontendTemplate($this->cal_template);
            $objTemplate->setData($arrEvent);

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
            $objTemplate->lastDay     = Date::parse(
                $GLOBALS['objPage']->datimFormat,
                $arrEvent['subscription_lastDay']
            );
            $objTemplate->addImage    = false;

            // Add image
            if ($arrEvent['addImage'] && is_file(TL_ROOT.'/'.$arrEvent['singleSRC'])) {
                if ($imgSize) {
                    $arrEvent['size'] = $imgSize;
                }

                $this->addImageToTemplate($objTemplate, $arrEvent);
                $objTemplate->href = $arrEvent['href'];
            }

            $objTemplate->enclosure = array();

            // Add enclosure
            if ($arrEvent['addEnclosure']) {
                $this->addEnclosuresToTemplate($objTemplate, $arrEvent);
            }

            $objTemplate->message = '';

            // Display the message
            if ($_SESSION['EVENT_SUBSCRIBE_MESSAGE'][$arrEvent['id']] != '') {
                $objTemplate->message = $_SESSION['EVENT_SUBSCRIBE_MESSAGE'][$arrEvent['id']];
                unset($_SESSION['EVENT_SUBSCRIBE_MESSAGE'][$arrEvent['id']]);
            }

            // Add form
            if ($canSubscribe || $isSubscribed) {
                $objTemplate->subscribed = $isSubscribed;
                $objTemplate->formId     = $strFormId;
                $objTemplate->action     = Environment::get('request');
                $objTemplate->showForm   = true;
                $objTemplate->submit     = !$isSubscribed ? $GLOBALS['TL_LANG']['MSC']['eventSubscribe'] : $GLOBALS['TL_LANG']['MSC']['eventUnsubscribe'];
            }

            $arrEvents[] = $objTemplate->parse();
        }

        $this->Template->events = $arrEvents;
    }
}
