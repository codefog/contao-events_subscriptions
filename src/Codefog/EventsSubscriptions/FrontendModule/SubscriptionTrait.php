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
use Codefog\EventsSubscriptions\MemberConfig;
use Codefog\EventsSubscriptions\Services;
use Contao\Controller;
use Contao\Date;
use Contao\FrontendUser;
use Contao\Input;
use Contao\PageModel;
use Haste\Form\Form;

trait SubscriptionTrait
{
    /**
     * Get the subscription basic data
     *
     * @param EventConfig $config
     *
     * @return array
     */
    protected function getSubscriptionBasicData(EventConfig $config)
    {
        $data = [
            'subscribeMessage'   => Services::getFlashMessage()->puke($config->getEvent()->id),
            'isEventPast'        => $this->event->startTime < time(),
            'isSubscribed'       => false,
            'subscribeEndTime'   => $this->getSubscribeEndTime($config),
            'unsubscribeEndTime' => $this->getUnsubscribeEndTime($config),
            'canSubscribe'       => false,
            'canUnsubscribe'     => false,
        ];

        if (FE_USER_LOGGED_IN) {
            $validator = Services::getSubscriptionValidator();
            $member    = MemberConfig::create(FrontendUser::getInstance()->id);

            $data['isSubscribed']   = $validator->isMemberSubscribed($config, $member);
            $data['canSubscribe']   = $validator->canMemberSubscribe($config, $member);
            $data['canUnsubscribe'] = $validator->canMemberUnsubscribe($config, $member);
        }

        return $data;
    }

    /**
     * Create the subscription form
     *
     * @param string $id
     *
     * @return Form
     */
    protected function createSubscriptionForm($id)
    {
        $form = new Form(
            $id,
            'POST',
            function ($haste) {
                return Input::post('FORM_SUBMIT') === $haste->getFormId();
            }
        );

        $form->addContaoHiddenFields();

        return $form;
    }

    /**
     * Process the subscription form
     *
     * @param EventConfig $config
     * @param array       $data
     */
    protected function processSubscriptionForm(EventConfig $config, array $data)
    {
        $event = $config->getEvent();

        if (!FE_USER_LOGGED_IN) {
            $this->handleRedirect(
                $data['jumpTo_login'],
                $GLOBALS['TL_LANG']['MSC']['events_subscriptions.login'],
                $event->id
            );
        }

        $user   = FrontendUser::getInstance();
        $member = MemberConfig::create($user->id);

        // Subscribe user
        if (Services::getSubscriptionValidator()->canMemberSubscribe($config, $member)) {
            Services::getSubscriber()->subscribeMember($event->id, $user->id);
            $this->handleRedirect(
                $data['jumpTo_subscribe'],
                $GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeConfirmation'],
                $event->id
            );
        }

        // Unsubscribe user
        if (Services::getSubscriptionValidator()->canMemberUnsubscribe($config, $member)) {
            Services::getSubscriber()->unsubscribeMember($event->id, $user->id);
            $this->handleRedirect(
                $data['jumpTo_unsubscribe'],
                $GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeConfirmation'],
                $event->id
            );
        }

        Controller::reload();
    }

    /**
     * Get the subscribe end time
     *
     * @param EventConfig $config
     *
     * @return array
     */
    protected function getSubscribeEndTime(EventConfig $config)
    {
        return $this->getFormattedTimes($config->getSubscribeEndTime());
    }

    /**
     * Get the unsubscribe end time
     *
     * @param EventConfig $config
     *
     * @return array
     */
    protected function getUnsubscribeEndTime(EventConfig $config)
    {
        return $this->getFormattedTimes($config->getUnsubscribeEndTime());
    }

    /**
     * Get the formatted times
     *
     * @param int $time
     *
     * @return array
     */
    private function getFormattedTimes($time)
    {
        return [
            'datim'  => Date::parse($GLOBALS['objPage']->datimFormat, $time),
            'date'   => Date::parse($GLOBALS['objPage']->dateFormat, $time),
            'time'   => Date::parse($GLOBALS['objPage']->timeFormat, $time),
            'tstamp' => $time,
        ];
    }

    /**
     * Handle the redirect
     *
     * @param int    $pageId
     * @param string $message
     * @param int    $eventId
     */
    private function handleRedirect($pageId, $message, $eventId)
    {
        if (($page = PageModel::findPublishedById($pageId)) !== null) {
            Controller::redirect($page->getFrontendUrl());
        }

        Services::getFlashMessage()->set($message, $eventId);
        Controller::reload();
    }
}
