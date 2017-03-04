<?php

namespace Codefog\EventsSubscriptions\FrontendModule;

use Codefog\EventsSubscriptions\EventConfig;
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
        $validator = Services::getSubscriptionValidator();
        $user      = FrontendUser::getInstance();

        return [
            'subscribeMessage'   => Services::getFlashMessage()->puke($config->getEvent()->id),
            'isEventPast'        => $this->event->startTime < time(),
            'isSubscribed'       => $validator->isMemberSubscribed($config, $user->id),
            'subscribeEndTime'   => $this->getSubscribeEndTime($config),
            'unsubscribeEndTime' => $this->getUnsubscribeEndTime($config),
            'canSubscribe'       => $validator->canMemberSubscribe($config, $user->id),
            'canUnsubscribe'     => $validator->canMemberUnsubscribe($config, $user->id),
        ];
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
            $this->handleRedirect($data['jumpTo_login'], $GLOBALS['TL_LANG']['MSC']['eventSubscribeLogin'], $event->id);
        }

        $user = FrontendUser::getInstance();

        // Subscribe user
        if (Services::getSubscriptionValidator()->canMemberSubscribe($config, $user->id)) {
            Services::getSubscriber()->subscribeMember($event->id, $user->id);
            $this->handleRedirect($data['jumpTo_subscribe'], $GLOBALS['TL_LANG']['MSC']['eventSubscribed'], $event->id);
        }

        // Unsubscribe user
        if (Services::getSubscriptionValidator()->canMemberUnsubscribe($config, $user->id)) {
            Services::getSubscriber()->unsubscribeMember($event->id, $user->id);
            $this->handleRedirect(
                $data['jumpTo_unsubscribe'],
                $GLOBALS['TL_LANG']['MSC']['eventUnsubscribed'],
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
