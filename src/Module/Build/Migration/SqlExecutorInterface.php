<?php

namespace Build\Migration;

/**
 * Spouští SQL skript migrace (více statementů).
 */
interface SqlExecutorInterface
{
    public function executeScript(string $sql): void;
}
