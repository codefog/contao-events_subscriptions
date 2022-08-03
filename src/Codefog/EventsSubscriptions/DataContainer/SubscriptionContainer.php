<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\DataContainer;

use Codefog\EventsSubscriptions\Event\SubscribeEvent;
use Codefog\EventsSubscriptions\Event\UnsubscribeEvent;
use Codefog\EventsSubscriptions\EventDispatcher;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Codefog\EventsSubscriptions\Services;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\Database;
use Contao\DataContainer;
use Contao\Date;
use Contao\Input;
use Contao\Message;
use Contao\System;

class SubscriptionContainer
{
    /**
     * Display the summary of subscriptions
     */
    public function displaySummary()
    {
        // Return if not a list view
        if ((int)CURRENT_ID !== (int)Input::get('id') || Input::get('act') || Input::get('key')) {
            return;
        }

        try {
            $config = Services::getEventConfigFactory()->create(CURRENT_ID);
        } catch (\Exception $e) {
            return;
        }

        $max = $config->getMaximumSubscriptions();

        // Get the waiting list settings
        if ($max > 0 && $config->hasWaitingList()) {
            $limit = $config->getWaitingListLimit();
            $max   = $limit ? ($max + $limit) : 0;
        }

        $count = Database::getInstance()
            ->prepare('SELECT SUM(numberOfParticipants) AS total FROM tl_calendar_events_subscription WHERE pid=?')
            ->execute($config->getEvent()->id)
            ->total
        ;

        if ($max > 0) {
            Message::addInfo(
                sprintf($GLOBALS['TL_LANG']['tl_calendar_events_subscription']['summaryMax'], $count, $max)
            );
        } else {
            Message::addInfo(sprintf($GLOBALS['TL_LANG']['tl_calendar_events_subscription']['summary'], $count));
        }
    }

    /**
     * Add send notification checkbox, if the record is not new.
     */
    public function addSendNotificationCheckbox(DataContainer $dc)
    {
        if (!$dc->id || Input::get('act') !== 'edit') {
            return;
        }

        $existing = Database::getInstance()
            ->prepare('SELECT dateCreated, type FROM tl_calendar_events_subscription WHERE id=?')
            ->execute($dc->id)
        ;

        if ($existing->type && !$existing->dateCreated) {
            PaletteManipulator::create()
                ->addField('sendNotification', 'type_legend', PaletteManipulator::POSITION_APPEND)
                ->applyToPalette($existing->type, 'tl_calendar_events_subscription')
            ;
        }
    }

    /**
     * Set the date created
     *
     * @param DataContainer $dc
     */
    public function setDateCreated(DataContainer $dc)
    {
        if (!$dc->activeRecord->dateCreated && Input::post('SUBMIT_TYPE') !== 'auto') {
            Database::getInstance()->prepare("UPDATE {$dc->table} SET dateCreated=? WHERE id=?")
                ->execute(time(), $dc->id);
        }
    }

    /**
     * Set the unsubscribe token
     *
     * @param DataContainer $dc
     */
    public function setUnsubscribeToken(DataContainer $dc)
    {
        if (!$dc->activeRecord->unsubscribeToken) {
            Database::getInstance()
                ->prepare("UPDATE {$dc->table} SET unsubscribeToken=? WHERE id=?")
                ->execute(SubscriptionModel::generateUnsubscribeToken(), $dc->id)
            ;
        }
    }

    /**
     * Dispatch the subscribe event
     *
     * @param DataContainer $dc
     */
    public function dispatchSubscribeEvent(DataContainer $dc)
    {
        if (Input::post('SUBMIT_TYPE') === 'auto' || $dc->activeRecord->dateCreated || ($subscriptionModel = SubscriptionModel::findByPk($dc->id)) === null) {
            return;
        }

        $subscription = Services::getSubscriptionFactory()->createFromModel($subscriptionModel);

        // Set the subscription model before checking if it's on waiting list
        $subscription->setSubscriptionModel($subscriptionModel);

        // Log the subscription
        if ($subscription->isOnWaitingList()) {
            $logMessage = '%s has been subscribed to a waiting list of the event "%s" (ID %s)';
        } else {
            $logMessage = '%s has been subscribed to the event "%s" (ID %s)';
        }

        System::log(sprintf($logMessage, strip_tags($subscription->getFrontendLabel()), $subscriptionModel->getEvent()->title, $subscriptionModel->getEvent()->id), __METHOD__, TL_GENERAL);

        $event = new SubscribeEvent($subscriptionModel, $subscription);
        $event->setExtras(['notification' => (bool) $dc->activeRecord->sendNotification]);

        // Dispatch the event
        Services::getEventDispatcher()->dispatch(EventDispatcher::EVENT_ON_SUBSCRIBE, $event);
    }

    /**
     * Dispatch the unsubscribe event
     *
     * @param DataContainer $dc
     */
    public function dispatchUnsubscribeEvent(DataContainer $dc)
    {
        if (($subscriptionModel = SubscriptionModel::findByPk($dc->id)) === null) {
            return;
        }

        $subscription = Services::getSubscriptionFactory()->createFromModel($subscriptionModel);
        $subscription->setSubscriptionModel($subscriptionModel);

        // Log the unsubscription
        if ($subscription->isOnWaitingList()) {
            $logMessage = '%s has been unsubscribed from a waiting list of the event "%s" (ID %s)';
        } else {
            $logMessage = '%s has been unsubscribed from the event "%s" (ID %s)';
        }

        System::log(sprintf($logMessage, strip_tags($subscription->getFrontendLabel()), $subscriptionModel->getEvent()->title, $subscriptionModel->getEvent()->id), __METHOD__, TL_GENERAL);

        // Dispatch the event
        Services::getEventDispatcher()->dispatch(EventDispatcher::EVENT_ON_UNSUBSCRIBE, new UnsubscribeEvent($subscriptionModel, $subscription));
    }

    /**
     * Generate the label
     *
     * @param array $row
     *
     * @return string
     */
    public function generateLabel(array $row)
    {
        $model = SubscriptionModel::findByPk($row['id']);

        try {
            $subscription = Services::getSubscriptionFactory()->createFromModel($model);
        } catch (\InvalidArgumentException $e) {
            return '';
        }

        return $subscription->getBackendLabel();
    }

    /**
     * Get the types
     *
     * @return array
     */
    public function getTypes()
    {
        return Services::getSubscriptionFactory()->getAll();
    }

    /**
     * Get all members and return them as array
     *
     * @param DataContainer $dc
     *
     * @return array
     */
    public function getMembers(DataContainer $dc)
    {
        $members = [
            $GLOBALS['TL_LANG']['tl_calendar_events_subscription']['activeMembers'] => [],
            $GLOBALS['TL_LANG']['tl_calendar_events_subscription']['inactiveMembers'] => [],
        ];

        $time = Date::floorToMinute();
        $records = Database::getInstance()
            ->prepare("SELECT * FROM tl_member WHERE id=? OR (id NOT IN (SELECT member FROM tl_calendar_events_subscription WHERE type=? AND pid=?)) ORDER BY lastname, firstname, username")
            ->execute($dc->activeRecord->member, 'member', $dc->activeRecord->pid)
        ;

        while ($records->next()) {
            $group = ($records->login && !$records->disable && (!$records->start || $records->start <= $time) && (!$records->stop || $records->stop > $time + 60)) ? $GLOBALS['TL_LANG']['tl_calendar_events_subscription']['activeMembers'] : $GLOBALS['TL_LANG']['tl_calendar_events_subscription']['inactiveMembers'];
            $members[$group][$records->id] = $records->lastname.' '.$records->firstname.' ('.$records->username.')';
        }

        return $members;
    }

    /**
     * Check if the member is already subscribed to the event
     *
     * @param int           $value
     * @param DataContainer $dc
     *
     * @return int
     *
     * @throws \Exception
     */
    public function checkIfAlreadyExists($value, DataContainer $dc)
    {
        $model = SubscriptionModel::findByPidAndMember($dc->activeRecord->pid, $value);

        if ($value && $model !== null && (int)$model->id !== (int)$dc->id) {
            throw new \Exception(
                sprintf($GLOBALS['TL_LANG']['ERR']['events_subscriptions.memberAlreadySubscribed'], $value)
            );
        }

        return $value;
    }
}
