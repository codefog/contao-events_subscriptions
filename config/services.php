<?php

declare(strict_types=1);

use Codefog\EventsSubscriptionsBundle\ExporterHelper;
use Codefog\EventsSubscriptionsBundle\NotificationCenterHelper;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autoconfigure()
        ->autowire()
    ;

    $services->load('Codefog\\EventsSubscriptionsBundle\\NotificationCenter\\', __DIR__ . '/../src/NotificationCenter');

    $services
        ->set(NotificationCenterHelper::class)
        ->public()
    ;

    $services
        ->set(ExporterHelper::class)
        ->public()
    ;
};
