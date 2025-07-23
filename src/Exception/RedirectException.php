<?php

namespace Codefog\EventsSubscriptionsBundle\Exception;

class RedirectException extends \RuntimeException
{
    /**
     * @var int
     */
    private $eventId;

    /**
     * @var string
     */
    private $page;

    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param string $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }
}
