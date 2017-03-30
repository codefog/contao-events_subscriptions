<?php

namespace Codefog\EventsSubscriptions\Subscription;

interface NotificationAwareInterface
{
    /**
     * Get the notification e-mail address
     *
     * @return string
     */
    public function getNotificationEmail();

    /**
     * Get the notification tokens
     *
     * @return array
     */
    public function getNotificationTokens();
}
