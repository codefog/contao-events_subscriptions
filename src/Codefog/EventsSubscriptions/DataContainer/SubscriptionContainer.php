<?php

namespace Codefog\EventsSubscriptions\DataContainer;

use Codefog\EventsSubscriptions\EventConfig;
use Codefog\EventsSubscriptions\SubscriptionValidator;
use Contao\Database;
use Contao\DataContainer;
use Contao\Input;
use Contao\MemberModel;
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
     * List all subscribed members
     *
     * @param array $row
     *
     * @return array
     */
    public function listMembers(array $row)
    {
        $member = MemberModel::findByPk($row['member']);

        return sprintf(
            '<div>%s %s <span style="color:#b3b3b3;padding-left:3px;">[%s - %s]</span></div>',
            $member->firstname,
            $member->lastname,
            $member->username,
            $member->email
        );
    }

    /**
     * Get all members and return them as array
     *
     * @return array
     */
    public function getMembers()
    {
        $members = [];
        $records = Database::getInstance()->execute("SELECT * FROM tl_member");

        while ($records->next()) {
            $members[$records->id] = $records->firstname.' '.$records->lastname.' ('.$records->username.')';
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
        $validator = new SubscriptionValidator();

        if ($value && $validator->isMemberSubscribed(EventConfig::create($dc->activeRecord->pid), $value)) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['memberAlreadySubscribed'], $value));
        }

        return $value;
    }
}
