<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\Model;

use Contao\CalendarEventsModel;
use Contao\MemberModel;
use Contao\Model;

class SubscriptionModel extends Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_calendar_events_subscription';

    /**
     * Get the event
     *
     * @return CalendarEventsModel|null
     */
    public function getEvent()
    {
        return CalendarEventsModel::findByPk($this->pid);
    }

    /**
     * Get the member
     *
     * @return MemberModel|null
     */
    public function getMember()
    {
        return MemberModel::findByPk($this->member);
    }

    /**
     * Generate the unsubscribe token
     *
     * @return string
     */
    public static function generateUnsubscribeToken()
    {
        return md5(uniqid('', true));
    }

    /**
     * Find the record by pid and member.
     */
    public static function findByPidAndMember($pid, $member)
    {
        return static::findOneBy(['pid=?', 'member=?', 'type=?'], [$pid, $member, 'member']);
    }
}
