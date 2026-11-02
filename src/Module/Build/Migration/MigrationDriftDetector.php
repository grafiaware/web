<?php

namespace Build\Migration;

/**
 * Porovná schema_migrations se soubory ve větvi — bez destruktivních akcí.
 */
final class MigrationDriftDetector
{
    public function __construct(
        private readonly MigrationRunner $runner,
    ) {
    }

    public function detect(MigrationTarget $target): MigrationDriftReport
    {
        $status = $this->runner->status($target);
        $pending = $status['pending'];
        $unknown = $status['unknown'];
        $hasPending = $pending !== [];
        $hasUnknown = $unknown !== [];

        $drift = match (true) {
            !$hasPending && !$hasUnknown => MigrationDriftStatus::Ok,
            $hasPending && !$hasUnknown => MigrationDriftStatus::Behind,
            !$hasPending && $hasUnknown => MigrationDriftStatus::Ahead,
            default => MigrationDriftStatus::Diverged,
        };

        return new MigrationDriftReport(
            $target,
            $drift,
            $pending,
            $unknown,
            $status['release_baselines']
        );
    }

    /**
     * @param list<MigrationTarget>|null $targets
     * @return list<MigrationDriftReport>
     */
    public function detectMany(?array $targets = null): array
    {
        $targets ??= MigrationTarget::all();
        $reports = [];
        foreach ($targets as $target) {
            $reports[] = $this->detect($target);
        }

        return $reports;
    }
}
