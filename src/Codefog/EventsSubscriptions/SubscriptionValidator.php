<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions;

use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Contao\Database;
use Contao\Date;

class SubscriptionValidator
{
    /**
     * Return true if the user can subscribe to the event
     *
     * @param EventConfig $event
     *
     * @return bool
     */
    public function canSubscribe(EventConfig $event)
    {
        if (!$this->validateMaximumSubscriptions($event)) {
            return false;
        }

        if (!$this->validateSubscribeEndTime($event)) {
            return false;
        }

        return true;
    }

    /**
     * Return true if the user can unsubscribe from the event
     *
     * @param EventConfig  $event
     *
     * @return bool
     */
    public function canUnsubscribe(EventConfig $event)
    {
        if (!$this->validateUnsubscribeEndTime($event)) {
            return false;
        }

        return true;
    }

    /**
     * Return true if member is subscribed
     *
     * @param EventConfig  $event
     * @param MemberConfig $member
     *
     * @return bool
     */
    public function isMemberSubscribed(EventConfig $event, MemberConfig $member)
    {
        if (!$event->canSubscribe()) {
            return false;
        }

        return SubscriptionModel::findOneBy(
            ['pid=? AND member=?'],
            [$event->getEvent()->id, $member->getMember()->id]
        ) !== null;
    }

    /**
     * Validate the maximum number of subscriptions
     *
     * @param EventConfig $event
     * @param int $numberOfParticipants
     * @param bool $ignoreWaitingList
     *
     * @return bool
     */
    public function validateMaximumSubscriptions(EventConfig $event, $numberOfParticipants = 1, $ignoreWaitingList = false)
    {
        if (!$event->canSubscribe()) {
            return false;
        }

        // Value is not set, unlimited number of subscriptions
        if (!($max = $event->getMaximumSubscriptions())) {
            return true;
        }

        // Check the waiting list
        if (!$ignoreWaitingList && $event->hasWaitingList()) {
            // Value is not set, unlimited number of subscriptions
            if (!($limit = $event->getWaitingListLimit())) {
                return true;
            }

            $max += $limit;
        }

        $total = Database::getInstance()
            ->prepare('SELECT SUM(numberOfParticipants) AS total FROM tl_calendar_events_subscription WHERE pid=?')
            ->execute($event->getEvent()->id)
            ->total
        ;

        return ($total + $numberOfParticipants) <= $max;
    }

    /**
     * Validate the subscribe end time
     *
     * @param EventConfig $event
     *
     * @return bool
     */
    public function validateSubscribeEndTime(EventConfig $event)
    {
        if (!$event->canSubscribe()) {
            return false;
        }

        return $event->getSubscribeEndTime() > time();
    }

    /**
     * Validate the unsubscribe end time
     *
     * @param EventConfig $event
     *
     * @return bool
     */
    public function validateUnsubscribeEndTime(EventConfig $event)
    {
        if (!$event->canSubscribe()) {
            return false;
        }

        return $event->getUnsubscribeEndTime() > time();
    }

    /**
     * Validate the member total limit
     *
     * @param MemberConfig $member
     *
     * @return bool
     */
    public function validateMemberTotalLimit(MemberConfig $member)
    {
        if (!($limit = $member->getTotalLimit())) {
            return true;
        }

        return $limit > SubscriptionModel::countBy('member', $member->getMember()->id);
    }

    /**
     * Validate the member period limit
     *
     * @param EventConfig  $event
     * @param MemberConfig $member
     *
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function validateMemberPeriodLimit(EventConfig $event, MemberConfig $member)
    {
        if (!$event->canSubscribe()) {
            return false;
        }

        // Value is not set, unlimited number of subscriptions
        if (($limit = $member->getPeriodLimit()) === null) {
            return true;
        }

        $date = new Date($event->getEvent()->startTime);

        switch ($limit['unit']) {
            case 'day':
                $from = $date->dayBegin;
                $to   = $date->dayEnd;
                break;
            case 'month':
                $from = $date->monthBegin;
                $to   = $date->monthEnd;
                break;
            case 'year':
                $from = $date->yearBegin;
                $to   = $date->yearEnd;
                break;
            default:
                throw new \RuntimeException(sprintf('The period unit "%s" is unsupported', $limit['unit']));
        }

        $subscriptions = SubscriptionModel::countBy(
            ['member=?', 'pid IN (SELECT id FROM tl_calendar_events WHERE startTime >= ? AND startTime <= ?)'],
            [$member->getMember()->id, $from, $to]
        );

        return $limit['value'] > $subscriptions;
    }

    /**
     * Validate the member groups
     *
     * @param EventConfig  $event
     * @param MemberConfig $member
     *
     * @return bool
     */
    public function validateMemberGroups(EventConfig $event, MemberConfig $member)
    {
        if (!$event->hasMemberGroupsLimit()) {
            return true;
        }

        $allowedGroups = $event->getMemberGroups();

        // Return false if no groups were selected
        if (count($allowedGroups) === 0) {
            return false;
        }

        return count(array_intersect($allowedGroups, $member->getMemberGroups())) > 0;
    }
}
