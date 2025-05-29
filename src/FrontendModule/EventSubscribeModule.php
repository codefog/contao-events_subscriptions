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

use Codefog\EventsSubscriptionsBundle\Services;
use Contao\BackendTemplate;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\Input;
use Contao\Module;
use Contao\StringUtil;
use Contao\System;

class EventSubscribeModule extends Module
{
    use SubscriptionTrait;

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_event_subscribe';

    /**
     * Current event
     * @var CalendarEventsModel
     */
    protected $event;

    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . $GLOBALS['TL_LANG']['FMD']['event_subscribe'][0] . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = StringUtil::specialcharsUrl(System::getContainer()->get('router')->generate('contao_backend', array('do'=>'themes', 'table'=>'tl_module', 'act'=>'edit', 'id'=>$this->id)));

            return $objTemplate->parse();
        }

        if (($this->event = $this->getEvent()) === null) {
            return '';
        }

        return parent::generate();
    }

    /**
     * Get the event
     *
     * @return CalendarEventsModel|null
     */
    protected function getEvent()
    {
        if (($calendars = CalendarModel::findAll()) === null) {
            return null;
        }

        return CalendarEventsModel::findPublishedByParentAndIdOrAlias(
            Input::get('auto_item'),
            $calendars->fetchEach('id')
        );
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        $data = $this->getSubscriptionTemplateData(Services::getEventConfigFactory()->create($this->event->id), $this->arrData);

        foreach ($data as $k => $v) {
            $this->Template->$k = $v;
        }

        $this->Template->event = $this->event->row();
    }
}
