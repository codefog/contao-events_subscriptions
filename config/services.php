<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autoconfigure()
        ->autowire()
    ;

    $services
        ->load('Codefog\\EventsSubscriptionsBundle\\', __DIR__ . '/../src')
        ->exclude(__DIR__ . '/../src/Backend')
        ->exclude(__DIR__ . '/../src/ContaoManager')
        ->exclude(__DIR__ . '/../src/DependencyInjection')
        ->exclude(__DIR__ . '/../src/Event')
        ->exclude(__DIR__ . '/../src/EventListener')
        ->exclude(__DIR__ . '/../src/Exception')
        ->exclude(__DIR__ . '/../src/FrontendModule')
        ->exclude(__DIR__ . '/../src/Subscription')
        ->exclude(__DIR__ . '/../src/Model')
        ->exclude(__DIR__ . '/../src/Automator.php')
        ->exclude(__DIR__ . '/../src/EventConfig.php')
        ->exclude(__DIR__ . '/../src/EventConfigFactory.php')
        ->exclude(__DIR__ . '/../src/EventDispatcher.php')
        ->exclude(__DIR__ . '/../src/Exporter.php')
        ->exclude(__DIR__ . '/../src/FlashMessage.php')
        ->exclude(__DIR__ . '/../src/MemberConfig.php')
        ->exclude(__DIR__ . '/../src/NotificationSender.php')
        ->exclude(__DIR__ . '/../src/Services.php')
        ->exclude(__DIR__ . '/../src/Subscriber.php')
        ->exclude(__DIR__ . '/../src/SubscriptionFactory.php')
        ->exclude(__DIR__ . '/../src/SubscriptionValidator.php')
    ;

    $services
        ->set(\Codefog\EventsSubscriptionsBundle\NotificationCenterHelper::class)
        ->public()
    ;
};
