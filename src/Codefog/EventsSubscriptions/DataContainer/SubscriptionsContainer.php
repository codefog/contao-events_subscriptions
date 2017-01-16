<?php

namespace Codefog\EventsSubscriptions\DataContainer;

use Contao\Database;
use Contao\DataContainer;
use Codefog\EventsSubscriptions\EventsSubscriptions;
use Contao\Input;
use Contao\Message;

class SubscriptionsContainer
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
            "SELECT subscription_maximum, (SELECT COUNT(*) FROM tl_calendar_events_subscriptions WHERE pid=tl_calendar_events.id) AS subscriptions FROM tl_calendar_events WHERE id=?"
        )
            ->limit(1)
            ->execute(CURRENT_ID);

        if (!$event->numRows) {
            return;
        }

        if ($event->subscription_maximum) {
            Message::addInfo(
                sprintf(
                    $GLOBALS['TL_LANG']['tl_calendar_events_subscriptions']['summaryMax'],
                    $event->subscriptions,
                    $event->subscription_maximum
                )
            );
        } else {
            Message::addInfo(
                sprintf($GLOBALS['TL_LANG']['tl_calendar_events_subscriptions']['summary'], $event->subscriptions)
            );
        }
    }

    /**
     * List all subscribed members
     *
     * @param array
     *
     * @return array
     */
    public function listMembers($arrRow)
    {
        $objMember = Database::getInstance()->prepare("SELECT * FROM tl_member WHERE id=?")
            ->limit(1)
            ->execute($arrRow['member']);

        return '<div>'.$objMember->firstname.' '.$objMember->lastname.' <span style="color:#b3b3b3;padding-left:3px;">['.$objMember->username.' - '.$objMember->email.']</span></div>';
    }


    /**
     * Get all members and return them as array
     * @return array
     */
    public function getMembers()
    {
        $arrMembers = array();
        $objMembers = Database::getInstance()->execute("SELECT * FROM tl_member");

        while ($objMembers->next()) {
            $arrMembers[$objMembers->id] = $objMembers->firstname.' '.$objMembers->lastname.' ('.$objMembers->username.')';
        }

        return $arrMembers;
    }


    /**
     * Check if the member is already subscribed to the event
     *
     * @param mixed
     * @param object
     *
     * @return mixed
     * @throws \Exception
     */
    public function checkIfAlreadyExists($varValue, DataContainer $dc)
    {
        if ($varValue && EventsSubscriptions::isSubscribed($dc->activeRecord->pid, $varValue)) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['memberAlreadySubscribed'], $varValue));
        }

        return $varValue;
    }
}
