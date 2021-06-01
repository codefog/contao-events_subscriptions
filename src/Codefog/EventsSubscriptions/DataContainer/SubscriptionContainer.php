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
use Contao\Database;
use Contao\DataContainer;
use Contao\Date;
use Contao\Input;
use Contao\Message;

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
        if ($config->hasWaitingList()) {
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
        if (Input::post('SUBMIT_TYPE') === 'auto' || $dc->activeRecord->dateCreated || ($subscription = SubscriptionModel::findByPk($dc->id)) === null) {
            return;
        }

        Services::getEventDispatcher()->dispatch(
            EventDispatcher::EVENT_ON_SUBSCRIBE,
            new SubscribeEvent($subscription, Services::getSubscriptionFactory()->createFromModel($subscription))
        );
    }

    /**
     * Dispatch the unsubscribe event
     *
     * @param DataContainer $dc
     */
    public function dispatchUnsubscribeEvent(DataContainer $dc)
    {
        if (($subscription = SubscriptionModel::findByPk($dc->id)) === null) {
            return;
        }

        Services::getEventDispatcher()->dispatch(
            EventDispatcher::EVENT_ON_UNSUBSCRIBE,
            new UnsubscribeEvent($subscription, Services::getSubscriptionFactory()->createFromModel($subscription))
        );
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

        return Services::getSubscriptionFactory()->createFromModel($model)->getBackendLabel();
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
        $members = [];
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
        $model = SubscriptionModel::findOneBy(['pid=? AND member=?'], [$dc->activeRecord->pid, $value]);

        if ($value && $model !== null && (int)$model->id !== (int)$dc->id) {
            throw new \Exception(
                sprintf($GLOBALS['TL_LANG']['ERR']['events_subscriptions.memberAlreadySubscribed'], $value)
            );
        }

        return $value;
    }
}
