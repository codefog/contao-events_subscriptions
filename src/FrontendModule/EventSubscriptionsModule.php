<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle\FrontendModule;

use Codefog\EventsSubscriptionsBundle\MemberConfig;
use Codefog\EventsSubscriptionsBundle\Services;
use Contao\BackendTemplate;
use Contao\CalendarEventsModel;
use Contao\Config;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\Date;
use Contao\Environment;
use Contao\Events;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\FrontendUser;
use Contao\Input;
use Contao\ModuleEventlist;
use Contao\Pagination;
use Contao\StringUtil;
use Contao\System;

class EventSubscriptionsModule extends ModuleEventlist
{
    use SubscriptionTrait;

    /**
     * Display a wildcard in the back end
     *
     * @return string
     */
    public function generate()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
        {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . $GLOBALS['TL_LANG']['FMD']['event_subscriptions'][0] . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = StringUtil::specialcharsUrl(System::getContainer()->get('router')->generate('contao_backend', array('do'=>'themes', 'table'=>'tl_module', 'act'=>'edit', 'id'=>$this->id)));

            return $objTemplate->parse();
        }

        if (!System::getContainer()->get('contao.security.token_checker')->hasFrontendUser()) {
            return '';
        }

        return parent::generate();
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        global $objPage;

        $blnClearInput = false;

        $intYear = (int) Input::get('year');
        $intMonth = (int) Input::get('month');
        $intDay = (int) Input::get('day');

        // Handle featured events
        $blnFeatured = null;

        if ($this->cal_featured == 'featured')
        {
            $blnFeatured = true;
        }
        elseif ($this->cal_featured == 'unfeatured')
        {
            $blnFeatured = false;
        }

        // Jump to the current period
        if (Input::get('year') === null && Input::get('month') === null && Input::get('day') === null)
        {
            switch ($this->cal_format)
            {
                case 'cal_year':
                    $intYear = date('Y');
                    break;

                case 'cal_month':
                    $intMonth = date('Ym');
                    break;

                case 'cal_day':
                    $intDay = date('Ymd');
                    break;
            }

            $blnClearInput = true;
        }

        $blnDynamicFormat = !$this->cal_ignoreDynamic && \in_array($this->cal_format, array('cal_day', 'cal_month', 'cal_year'));

        // Create the date object
        try
        {
            if ($blnDynamicFormat && $intYear)
            {
                $this->Date = new Date($intYear, 'Y');
                $this->cal_format = 'cal_year';
                $this->headline .= ' ' . date('Y', $this->Date->tstamp);
            }
            elseif ($blnDynamicFormat && $intMonth)
            {
                $this->Date = new Date($intMonth, 'Ym');
                $this->cal_format = 'cal_month';
                $this->headline .= ' ' . Date::parse('F Y', $this->Date->tstamp);
            }
            elseif ($blnDynamicFormat && $intDay)
            {
                $this->Date = new Date($intDay, 'Ymd');
                $this->cal_format = 'cal_day';
                $this->headline .= ' ' . Date::parse($objPage->dateFormat, $this->Date->tstamp);
            }
            else
            {
                $this->Date = new Date();
            }
        }
        catch (\OutOfBoundsException $e)
        {
            throw new PageNotFoundException('Page not found: ' . Environment::get('uri'));
        }

        [$intStart, $intEnd, $strEmpty] = $this->getDatesFromFormat($this->Date, $this->cal_format);

        // Use the start of the day when filtering events in line 197 (see #4476)
        $intStartFloored = strtotime(date('Y-m-d', $intStart) . ' 00:00:00');

        // Get all events
        $arrAllEvents = $this->getAllEvents($this->cal_calendar, $intStart, $intEnd, $blnFeatured);

        $sort = ($this->cal_order == 'descending') ? 'krsort' : 'ksort';

        // Sort the days
        $sort($arrAllEvents);

        // Sort the events
        foreach (array_keys($arrAllEvents) as $key)
        {
            $sort($arrAllEvents[$key]);
        }

        $intCount = 0;
        $arrEvents = array();

        $memberConfig = MemberConfig::create(FrontendUser::getInstance()->id);
        $validator    = Services::getSubscriptionValidator();

        // Remove events outside the scope
        foreach ($arrAllEvents as $key => $days) {
            foreach ($days as $day => $events) {
                // Skip events before the start day if the "shortened view" option is not set.
                // Events after the end day are filtered in the Events::addEvent() method (see #8782).
                if (!$this->cal_noSpan && $day < $intStartFloored) {
                    continue;
                }

                foreach ($events as $event) {
                    // Use repeatEnd if > 0 (see #8447)
                    if ($event['startTime'] > $intEnd || ($event['repeatEnd'] ?: $event['effectiveEndTime']) < $intStart) {
                        continue;
                    }

                    // Hide running events
                    if ($this->cal_hideRunning && $event['begin'] < $intStart) {
                        continue;
                    }

                    // Skip occurrences in the past
                    if ($event['repeatEnd'] && $event['end'] < $intStart) {
                        continue;
                    }

                    // Hide running non-recurring events (see #30)
                    if ($this->cal_hideRunning && !$event['recurring'] && $event['startTime'] < time() && $event['effectiveEndTime'] > time()) {
                        continue;
                    }

                    $event['subscription_config'] = Services::getEventConfigFactory()->create($event['id']);

                    // The user is not subscribed to the event
                    if (!$validator->isMemberSubscribed($event['subscription_config'], $memberConfig)) {
                        continue;
                    }

                    $event['firstDay'] = $GLOBALS['TL_LANG']['DAYS'][date('w', (int) $day)];
                    $event['firstDate'] = Date::parse($objPage->dateFormat, $day);
                    $event['count'] = ++$intCount; // see #74

                    $arrEvents[] = $event;
                }
            }
        }

        unset($arrAllEvents);

        // Limit the number of recurrences if both the event list and the event
        // allow unlimited recurrences (see #4037)
        if (!$this->numberOfItems)
        {
            $unset = array();

            foreach ($arrEvents as $k=>$v)
            {
                if ($v['recurring'] && !$v['recurrences'])
                {
                    if (!isset($unset[$v['id']]))
                    {
                        $unset[$v['id']] = true;
                    }
                    else
                    {
                        unset($arrEvents[$k]);
                    }
                }
            }

            unset($unset);
            $arrEvents = array_values($arrEvents);
        }

        $total = \count($arrEvents);
        $limit = $total;
        $offset = 0;

        // Overall limit
        if ($this->cal_limit > 0)
        {
            $total = min($this->cal_limit, $total);
            $limit = $total;
        }

        // Pagination
        if ($this->perPage > 0)
        {
            $id = 'page_e' . $this->id;
            $page = (int) (Input::get($id) ?? 1);

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
            {
                throw new PageNotFoundException('Page not found: ' . Environment::get('uri'));
            }

            $offset = ($page - 1) * $this->perPage;
            $limit = min($this->perPage + $offset, $total);

            $objPagination = new Pagination($total, $this->perPage, Config::get('maxPaginationLinks'), $id);
            $this->Template->pagination = $objPagination->generate("\n  ");
        }

        $strMonth = '';
        $strDate = '';
        $strEvents = '';
        $eventCount = 0;

        $uuids = array();

        for ($i=$offset; $i<$limit; $i++)
        {
            if ($arrEvents[$i]['addImage'] && $arrEvents[$i]['singleSRC'])
            {
                $uuids[] = $arrEvents[$i]['singleSRC'];
            }
        }

        // Preload all images in one query, so they are loaded into the model registry
        FilesModel::findMultipleByUuids($uuids);

        // Parse events
        for ($i=$offset; $i<$limit; $i++)
        {
            $event = $arrEvents[$i];

            $objTemplate = new FrontendTemplate($this->cal_template ?: 'event_list');
            $objTemplate->setData($event);

            $subscriptionData = $this->getSubscriptionTemplateData(Services::getEventConfigFactory()->create($event['id']), $this->arrData);

            // Add the subscription data to the template
            foreach ($subscriptionData as $k => $v) {
                $objTemplate->$k = $v;
            }

            // Month header
            if ($strMonth != $event['month'])
            {
                $objTemplate->newMonth = true;
                $strMonth = $event['month'];
            }

            // Day header
            if ($strDate != $event['firstDate'])
            {
                $objTemplate->header = true;
                $strDate = $event['firstDate'];
            }

            // Show the teaser text of redirect events (see #6315)
            if (\is_bool($event['details']) && $event['source'] == 'default')
            {
                $objTemplate->hasDetails = false;
            }

            $objTemplate->hasReader = $event['source'] == 'default';

            // Add the template variables
            $objTemplate->classList = $event['class'] . ' cal_' . $event['parent'];
            $objTemplate->classUpcoming = $event['class'] . ' cal_' . $event['parent'];
            $objTemplate->readMore = StringUtil::specialchars(\sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $event['title']));
            $objTemplate->more = $event['linkText'] ?: $GLOBALS['TL_LANG']['MSC']['more'];
            $objTemplate->locationLabel = $GLOBALS['TL_LANG']['MSC']['location'];

            // Short view
            if ($this->cal_noSpan)
            {
                $objTemplate->day = $event['day'];
                $objTemplate->date = $event['date'];
            }
            else
            {
                $objTemplate->day = $event['firstDay'];
                $objTemplate->date = $event['firstDate'];
            }

            $objTemplate->addImage = false;
            $objTemplate->addBefore = false;

            // Add an image
            if ($event['addImage'])
            {
                $eventModel = CalendarEventsModel::findById($event['id']);
                $imgSize = $eventModel->size ?: null;

                // Override the default image size
                if ($this->imgSize)
                {
                    $size = StringUtil::deserialize($this->imgSize);

                    if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]) || ($size[2][0] ?? null) === '_')
                    {
                        $imgSize = $this->imgSize;
                    }
                }

                $figureBuilder = System::getContainer()->get('contao.image.studio')->createFigureBuilder();

                $figure = $figureBuilder
                    ->from($event['singleSRC'])
                    ->setSize($imgSize)
                    ->setOverwriteMetadata($eventModel->getOverwriteMetadata())
                    ->enableLightbox($eventModel->fullsize)
                    ->buildIfResourceExists();

                if (null !== $figure)
                {
                    // Rebuild with link to event if none is set
                    if (!$figure->getLinkHref())
                    {
                        $figure = $figureBuilder
                            ->setLinkHref($event['href'])
                            ->setLinkAttribute('title', $objTemplate->readMore)
                            ->build();
                    }

                    $figure->applyLegacyTemplateData($objTemplate, null, $eventModel->floating);
                }
            }

            $objTemplate->enclosure = array();

            // Add enclosure
            if ($event['addEnclosure'])
            {
                $this->addEnclosuresToTemplate($objTemplate, $event);
            }

            // schema.org information
            $objTemplate->getSchemaOrgData = static function () use ($event, $objTemplate): array {
                $jsonLd = Events::getSchemaOrgData((new CalendarEventsModel())->setRow($event));

                if ($objTemplate->addImage && $objTemplate->figure)
                {
                    $jsonLd['image'] = $objTemplate->figure->getSchemaOrgData();
                }

                return $jsonLd;
            };

            $strEvents .= $objTemplate->parse();

            ++$eventCount;
        }

        // No events found
        if (!$strEvents)
        {
            $strEvents = "\n" . '<div class="empty">' . $strEmpty . '</div>' . "\n";
        }

        // See #3672
        $this->Template->headline = $this->headline;
        $this->Template->events = $strEvents;
        $this->Template->eventCount = $eventCount;

        // Clear the $_GET array (see #2445)
        if ($blnClearInput)
        {
            Input::setGet('year', null);
            Input::setGet('month', null);
            Input::setGet('day', null);
        }
    }
}
