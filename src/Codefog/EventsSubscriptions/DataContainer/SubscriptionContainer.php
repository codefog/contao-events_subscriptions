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
        if ((int)CURRENT_ID !== (int)Input::get('id')) {
            return;
        }

        $event = Database::getInstance()->prepare(
            "SELECT subscription_maximum, (SELECT COUNT(*) FROM tl_calendar_events_subscription WHERE pid=tl_calendar_events.id) AS subscriptions FROM tl_calendar_events WHERE id=?"
        )
            ->limit(1)
            ->execute(CURRENT_ID);

        if (!$event->numRows) {
            return;
        }

        if ($event->subscription_maximum) {
            Message::addInfo(
                sprintf(
                    $GLOBALS['TL_LANG']['tl_calendar_events_subscription']['summaryMax'],
                    $event->subscriptions,
                    $event->subscription_maximum
                )
            );
        } else {
            Message::addInfo(
                sprintf($GLOBALS['TL_LANG']['tl_calendar_events_subscription']['summary'], $event->subscriptions)
            );
        }
    }

    /**
     * Dispatch the subscribe event
     *
     * @param DataContainer $dc
     */
    public function dispatchSubscribeEvent(DataContainer $dc)
    {
        if ($dc->activeRecord->tstamp || ($subscription = SubscriptionModel::findByPk($dc->id)) === null) {
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
     * @return array
     */
    public function getMembers()
    {
        $members = [];
        $records = Database::getInstance()->execute("SELECT * FROM tl_member ORDER BY lastname, firstname, username");

        while ($records->next()) {
            $members[$records->id] = $records->lastname.' '.$records->firstname.' ('.$records->username.')';
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
