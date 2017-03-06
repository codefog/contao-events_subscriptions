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
use Contao\Date;

class SubscriptionValidator
{
    /**
     * Return true if the member can subscribe to the event
     *
     * @param EventConfig  $event
     * @param MemberConfig $member
     *
     * @return bool
     */
    public function canMemberSubscribe(EventConfig $event, MemberConfig $member)
    {
        if ($this->isMemberSubscribed($event, $member)) {
            return false;
        }

        if (!$this->validateMaximumSubscriptions($event)) {
            return false;
        }

        if (!$this->validateSubscribeEndTime($event)) {
            return false;
        }

        if (!$this->validateMemberTotalLimit($member)) {
            return false;
        }

        if (!$this->validateMemberPeriodLimit($event, $member)) {
            return false;
        }

        return true;
    }

    /**
     * Return true if the member can unsubscribe from the event
     *
     * @param EventConfig  $event
     * @param MemberConfig $member
     *
     * @return bool
     */
    public function canMemberUnsubscribe(EventConfig $event, MemberConfig $member)
    {
        if (!$this->isMemberSubscribed($event, $member)) {
            return false;
        }

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
     *
     * @return bool
     */
    public function validateMaximumSubscriptions(EventConfig $event)
    {
        if (!$event->canSubscribe()) {
            return false;
        }

        // Value is not set, unlimited number of subscriptions
        if (!($max = $event->getMaximumSubscriptions())) {
            return true;
        }

        return SubscriptionModel::countBy('pid', $event->getEvent()->id) < $max;
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
}
