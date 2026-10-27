<?php

namespace Build\Migration;

use Pes\Database\Manipulator\Manipulator;

/**
 * Executor nad PES Manipulator (produkční / Build cesta).
 */
final class ManipulatorSqlExecutor implements SqlExecutorInterface
{
    public function __construct(
        private readonly Manipulator $manipulator,
    ) {
    }

    public function executeScript(string $sql): void
    {
        $sql = trim($sql);
        if ($sql === '') {
            return;
        }
        $this->manipulator->executeSequence($sql);
    }
}
