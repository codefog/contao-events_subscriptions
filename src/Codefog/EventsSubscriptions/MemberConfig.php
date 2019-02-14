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

use Contao\MemberGroupModel;
use Contao\MemberModel;
use Contao\Model\Collection;

class MemberConfig
{
    /**
     * @var MemberModel
     */
    private $member;

    /**
     * @var Collection
     */
    private $groups;

    /**
     * MemberConfig constructor.
     *
     * @param MemberModel $member
     * @param Collection  $groups
     */
    public function __construct(MemberModel $member, Collection $groups)
    {
        $this->member = $member;
        $this->groups = $groups;
    }

    /**
     * Get the member model
     *
     * @return MemberModel|null
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Get the member groups
     *
     * @return array
     */
    public function getMemberGroups()
    {
        return deserialize($this->member->groups, true);
    }

    /**
     * Get the total subscription limit
     *
     * @return int
     */
    public function getTotalLimit()
    {
        if ($this->member->subscription_enableLimit) {
            return (int)$this->member->subscription_totalLimit;
        }

        $limit = 0;

        /** @var MemberGroupModel $group */
        foreach ($this->groups as $group) {
            if ($group->subscription_enableLimit && $group->subscription_totalLimit > $limit) {
                $limit = (int)$group->subscription_totalLimit;
            }
        }

        return $limit;
    }

    /**
     * Get the period subscription limit
     *
     * @return array|null
     */
    public function getPeriodLimit()
    {
        if ($this->member->subscription_enableLimit) {
            $data = deserialize($this->member->subscription_periodLimit, true);

            if ($data['value'] > 0) {
                return $data;
            }
        }

        /** @var MemberGroupModel $group */
        foreach ($this->groups as $group) {
            if ($group->subscription_enableLimit) {
                $data = deserialize($group->subscription_periodLimit, true);

                if ($data['value'] > 0) {
                    return $data;
                }
            }
        }

        return null;
    }

    /**
     * Create the instance by member ID
     *
     * @param int $memberId
     *
     * @return MemberConfig
     *
     * @throws \InvalidArgumentException
     */
    public static function create($memberId)
    {
        if (($member = MemberModel::findByPk($memberId)) === null) {
            throw new \InvalidArgumentException(sprintf('The member ID "%s" does not exist', $memberId));
        }

        if (($groups = MemberGroupModel::findMultipleByIds(deserialize($member->groups, true))) === null) {
            throw new \InvalidArgumentException(sprintf('The member ID "%s" has no member groups', $member->id));
        }

        return new static($member, $groups);
    }
}
