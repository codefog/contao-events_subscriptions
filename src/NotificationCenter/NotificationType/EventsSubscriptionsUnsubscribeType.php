<?php

namespace Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType;

use Terminal42\NotificationCenterBundle\NotificationType\NotificationTypeInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\AnythingTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\EmailTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\Factory\TokenDefinitionFactoryInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\TextTokenDefinition;

class EventsSubscriptionsUnsubscribeType implements NotificationTypeInterface
{
    public const NAME = 'events_subscriptions_unsubscribe';

    public function __construct(private TokenDefinitionFactoryInterface $factory)
    {
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTokenDefinitions(): array
    {
        return [
            $this->factory->create(AnythingTokenDefinition::class, 'calendar_*', ''),
            $this->factory->create(AnythingTokenDefinition::class, 'event_*', ''),
            $this->factory->create(AnythingTokenDefinition::class, 'subscription_*', ''),

            $this->factory->create(EmailTokenDefinition::class, 'recipient_email', ''),

            $this->factory->create(TextTokenDefinition::class, 'days_before_event', ''),
            $this->factory->create(TextTokenDefinition::class, 'event_link', ''),
        ];
    }
}
