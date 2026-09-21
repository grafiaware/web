<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Container;

use Container\AuthDbContainerConfigurator;
use Test\Support\TestDatabaseEnv;

/**
 * Auth DB konfigurátor pro testy — volitelný override z TEST_DB_* env.
 */
class TestAuthDbContainerConfigurator extends AuthDbContainerConfigurator
{
    public function getParams(): iterable
    {
        return TestDatabaseEnv::authConnectionParams() + parent::getParams();
    }
}
