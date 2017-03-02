<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * Copyright (C) 2013 Codefog
 *
 * @package events_subscriptions
 * @author  Codefog <http://codefog.pl>
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */

namespace Codefog\EventsSubscriptions;

class EventsSubscriptions
{
    /**
     * Return true if the member can subscribe to the event
     *
     * @param int $eventId
     * @param int $memberId
     *
     * @return bool
     */
    public static function canSubscribe($eventId, $memberId)
    {
        $validator = new SubscriptionValidator();

        return $validator->canMemberSubscribe(EventConfig::create($eventId), $memberId);
    }

    /**
     * Subscribe the member and return true on success, false otherwise
     *
     * @param int $eventId
     * @param int $memberId
     *
     * @return boolean
     */
    public static function subscribeMember($eventId, $memberId)
    {
        $subscriber = new Subscriber();
        $subscriber->subscribeMember($eventId, $memberId);

        return true;
    }

    /**
     * Unsubscribe the member and return true on success, false otherwise
     *
     * @param int $eventId
     * @param int $memberId
     *
     * @return boolean
     */
    public static function unsubscribeMember($eventId, $memberId)
    {
        $subscriber = new Subscriber();

        return $subscriber->unsubscribeMember($eventId, $memberId);
    }

    /**
     * Return true if the member is subscribed
     *
     * @param int $eventId
     * @param int $memberId
     *
     * @return boolean
     */
    public static function isSubscribed($eventId, $memberId)
    {
        $validator = new SubscriptionValidator();

        return $validator->isMemberSubscribed(EventConfig::create($eventId), $memberId);
    }
}
