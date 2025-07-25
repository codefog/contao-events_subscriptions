<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle\EventListener;

use Codefog\EventsSubscriptionsBundle\MemberConfig;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Contao\Date;
use Contao\FrontendUser;
use Contao\StringUtil;
use Contao\System;

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
        $chunks = StringUtil::trimsplit('::', $tag);

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
     * @return string|int
     */
    private function getTotalLimit()
    {
        if (!System::getContainer()->get('contao.security.token_checker')->hasFrontendUser()) {
            return '';
        }

        $member = MemberConfig::create(FrontendUser::getInstance()->id);
        $limit  = $member->getTotalLimit();

        return ($limit > 0) ? $limit : '';
    }

    /**
     * Get the total limit left
     *
     * @return string|int
     */
    private function getTotalLimitLeft()
    {
        if (($limit = $this->getTotalLimit()) === '') {
            return '';
        }

        return $limit - SubscriptionModel::countBy('member', FrontendUser::getInstance()->id);
    }

    /**
     * Get the period limit
     *
     * @param string $key
     *
     * @return string|int
     */
    private function getPeriodLimit($key)
    {
        if (!System::getContainer()->get('contao.security.token_checker')->hasFrontendUser() || ($key !== 'unit' && $key !== 'value')) {
            return '';
        }

        $member = MemberConfig::create(FrontendUser::getInstance()->id);

        if (($limit = $member->getPeriodLimit()) === null) {
            return '';
        }

        return $limit[$key];
    }

    /**
     * Get the period limit left
     *
     * @param string $key
     *
     * @return string|int
     */
    private function getPeriodLimitLeft($key)
    {
        if (!System::getContainer()->get('contao.security.token_checker')->hasFrontendUser() || ($key !== 'unit' && $key !== 'value')) {
            return '';
        }

        $member = MemberConfig::create(FrontendUser::getInstance()->id);

        if (($limit = $member->getPeriodLimit()) === null) {
            return '';
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
