<?php

namespace Codefog\EventsSubscriptionsBundle\Model;

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

    public function getEvent(): CalendarEventsModel|null
    {
        return $this->pid ? CalendarEventsModel::findByPk($this->pid) : null;
    }

    public function getMember(): MemberModel|null
    {
        return $this->member ? MemberModel::findByPk($this->member) : null;
    }

    public static function generateUnsubscribeToken(): string
    {
        return md5(uniqid('', true));
    }

    public static function findByPidAndMember($pid, $member): static|null
    {
        return static::findOneBy(['pid=?', 'member=?', 'type=?'], [$pid, $member, 'member']);
    }
}
