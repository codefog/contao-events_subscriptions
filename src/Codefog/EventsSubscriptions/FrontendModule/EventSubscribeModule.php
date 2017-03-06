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
use Contao\BackendTemplate;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Haste\Input\Input;

class SubscribeModule extends \Module
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
        if (TL_MODE === 'BE') {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['event_subscribe'][0]).' ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

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
            Input::getAutoItem('events'),
            $calendars->fetchEach('id')
        );
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        $config = EventConfig::create($this->event->id);
        $data   = $this->getSubscriptionBasicData($config);

        // Add the subscription form
        if ($data['canSubscribe'] || $data['canUnsubscribe']) {
            $form = $this->createSubscriptionForm('event-subscription-'.$this->id);

            if ($form->validate()) {
                $this->processSubscriptionForm($config, $this->arrData);
            }

            $this->Template->subscriptionForm = $form->getHelperObject();
        }

        // Add the subscription data to the template
        foreach ($data as $k => $v) {
            $this->Template->$k = $v;
        }
    }
}
