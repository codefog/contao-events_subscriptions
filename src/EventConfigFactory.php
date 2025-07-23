<?php

namespace Codefog\EventsSubscriptionsBundle;

class EventConfigFactory
{
    /**
     * Create the event config
     *
     * @param int $eventId
     *
     * @return EventConfig
     */
    public function create($eventId)
    {
        return EventConfig::create($eventId);
    }
}
