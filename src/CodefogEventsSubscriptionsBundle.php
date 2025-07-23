<?php

namespace Codefog\EventsSubscriptionsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CodefogEventsSubscriptionsBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
