<?php

declare(strict_types=1);

namespace Test\Unit\Build\Migration;

use Build\Migration\HandlerPdoUnwrapper;
use PDO;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

final class HandlerPdoUnwrapperTest extends TestCase
{
    public function testUnwrapReturnsPdoInstanceDirectly(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $this->assertSame($pdo, HandlerPdoUnwrapper::unwrap($pdo));
    }

    public function testUnwrapReadsPrivateConnectionProperty(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $handler = new class ($pdo) {
            public function __construct(private PDO $connection)
            {
            }
        };

        $this->assertSame($pdo, HandlerPdoUnwrapper::unwrap($handler));
    }

    public function testUnwrapFailsWithoutConnectionProperty(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('neobsahuje property connection');
        HandlerPdoUnwrapper::unwrap(new stdClass());
    }

    public function testUnwrapFailsWhenConnectionIsNotPdo(): void
    {
        $handler = new class {
            private string $connection = 'nope';
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('musí být PDO');
        HandlerPdoUnwrapper::unwrap($handler);
    }
}
