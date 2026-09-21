<?php
declare(strict_types=1);

namespace Test\Integration\Event\Container;

use Container\EventsModelContainerConfigurator;
use Test\Support\TestDatabaseEnv;

/**
 * Events model konfigurátor pro testy — volitelný override účtu/DB z TEST_DB_* env.
 */
class TestEventsModelContainerConfigurator extends EventsModelContainerConfigurator
{
    public function getParams(): iterable
    {
        return TestDatabaseEnv::eventsConnectionParams() + parent::getParams();
    }
}
