<?php

declare(strict_types=1);

namespace Build\Migration;

use RuntimeException;

final class SchemaStructureMismatchException extends RuntimeException
{
    public function __construct(
        public readonly MigrationTarget $target,
        public readonly SchemaStructureDiff $diff,
        public readonly string $dumpPath,
    ) {
        parent::__construct($diff->format($target->value, $dumpPath));
    }
}
