<?php

namespace Codefog\EventsSubscriptions\EventListener;

use Codefog\EventsSubscriptions\MemberConfig;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Contao\Date;
use Contao\FrontendUser;

class InsertTagsListener
{
    /**
     * On replace insert tags. Available insert tags:
     *
     *     - {{events_subscriptions::total_limit}}
     *     - {{events_subscriptions::total_limit_left}}
     *     - {{events_subscriptions::period_limit::$key}} where $key is "value" or "unit"
     *     - {{events_subscriptions::period_limit_left::$key}} where $key is "value" or "unit"
     *
     * @param string $tag
     *
     * @return string|bool
     */
    public function onReplace($tag)
    {
        $chunks = trimsplit('::', $tag);

        if ($chunks[0] === 'events_subscriptions') {
            switch ($chunks[1]) {
                case 'total_limit':
                    return $this->getTotalLimit();

                case 'total_limit_left':
                    return $this->getTotalLimitLeft();

                case 'period_limit':
                    return $this->getPeriodLimit($chunks[2]);

                case 'period_limit_left':
                    return $this->getPeriodLimitLeft($chunks[2]);
            }
        }

        return false;
    }

    /**
     * Get the total limit
     *
     * @return bool|int
     */
    private function getTotalLimit()
    {
        if (!FE_USER_LOGGED_IN) {
            return false;
        }

        $member = MemberConfig::create(FrontendUser::getInstance()->id);
        $limit  = $member->getTotalLimit();

        return ($limit > 0) ? $limit : false;
    }

    /**
     * Get the total limit left
     *
     * @return bool|int
     */
    private function getTotalLimitLeft()
    {
        if (($limit = $this->getTotalLimit()) === false) {
            return false;
        }

        return $limit - SubscriptionModel::countBy('member', FrontendUser::getInstance()->id);
    }

    /**
     * Get the period limit
     *
     * @param string $key
     *
     * @return bool|int
     */
    private function getPeriodLimit($key)
    {
        if (!FE_USER_LOGGED_IN || ($key !== 'unit' && $key !== 'value')) {
            return false;
        }

        $member = MemberConfig::create(FrontendUser::getInstance()->id);

        if (($limit = $member->getPeriodLimit()) === null) {
            return false;
        }

        return $limit[$key];
    }

    /**
     * Get the period limit left
     *
     * @param string $key
     *
     * @return bool|int
     */
    private function getPeriodLimitLeft($key)
    {
        if (!FE_USER_LOGGED_IN || ($key !== 'unit' && $key !== 'value')) {
            return false;
        }

        $member = MemberConfig::create(FrontendUser::getInstance()->id);

        if (($limit = $member->getPeriodLimit()) === null) {
            return false;
        }

        // Return the unit immediately
        if ($key === 'unit') {
            return $limit[$key];
        }

        $date = new Date();

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

        return $limit['value'] - $subscriptions;
    }


}
