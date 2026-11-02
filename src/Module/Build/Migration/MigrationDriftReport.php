<?php

namespace Build\Migration;

/**
 * Výsledek porovnání schema_migrations vs. soubory migrations/{target}/.
 */
final class MigrationDriftReport
{
    /**
     * @param list<string> $pending
     * @param list<string> $unknown
     * @param list<string> $releaseBaselines
     */
    public function __construct(
        public readonly MigrationTarget $target,
        public readonly MigrationDriftStatus $status,
        public readonly array $pending,
        public readonly array $unknown,
        public readonly array $releaseBaselines,
    ) {
    }

    public function isOk(): bool
    {
        return $this->status === MigrationDriftStatus::Ok;
    }

    public function isAhead(): bool
    {
        return $this->status === MigrationDriftStatus::Ahead
            || $this->status === MigrationDriftStatus::Diverged;
    }

    public function isBehind(): bool
    {
        return $this->status === MigrationDriftStatus::Behind
            || $this->status === MigrationDriftStatus::Diverged;
    }

    public function summary(): string
    {
        return sprintf(
            '%s=%s pending=%d unknown=%d baselines=[%s]',
            $this->target->value,
            $this->status->value,
            count($this->pending),
            count($this->unknown),
            implode(',', $this->releaseBaselines)
        );
    }
}
