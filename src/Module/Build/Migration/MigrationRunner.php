<?php

namespace Build\Migration;

/**
 * Aplikuje pending migrace a umí baseline (zápis bez spuštění SQL).
 */
final class MigrationRunner
{
    public function __construct(
        private readonly MigrationScanner $scanner,
        private readonly MigrationJournal $journal,
        private readonly SqlExecutorInterface $executor,
    ) {
    }

    /**
     * @return array{
     *   target: string,
     *   applied: list<string>,
     *   pending: list<string>,
     *   unknown: list<string>
     * }
     */
    public function status(MigrationTarget $target): array
    {
        $definitions = $this->scanner->scan($target);
        $fileVersions = array_map(static fn (MigrationDefinition $d): string => $d->version, $definitions);
        $applied = $this->journal->getAppliedVersions();
        $appliedSet = array_fill_keys($applied, true);
        $fileSet = array_fill_keys($fileVersions, true);

        $pending = [];
        foreach ($fileVersions as $version) {
            if (!isset($appliedSet[$version])) {
                $pending[] = $version;
            }
        }

        $unknown = [];
        foreach ($applied as $version) {
            if (!isset($fileSet[$version])) {
                $unknown[] = $version;
            }
        }

        return [
            'target' => $target->value,
            'applied' => $applied,
            'pending' => $pending,
            'unknown' => $unknown,
        ];
    }

    /**
     * @return list<string> verze právě aplikované
     */
    public function migrate(MigrationTarget $target): array
    {
        $this->journal->ensureTable();
        $appliedNow = [];
        foreach ($this->scanner->scan($target) as $definition) {
            if ($this->journal->isApplied($definition->version)) {
                continue;
            }
            $this->executor->executeScript($definition->readSql());
            $this->journal->record($definition->version);
            $appliedNow[] = $definition->version;
        }

        return $appliedNow;
    }

    /**
     * Označí všechny souborové migrace jako applied bez spuštění SQL.
     *
     * @return list<string> nově zapsané verze
     */
    public function baseline(MigrationTarget $target): array
    {
        $this->journal->ensureTable();
        $recorded = [];
        foreach ($this->scanner->scan($target) as $definition) {
            if ($this->journal->isApplied($definition->version)) {
                continue;
            }
            $this->journal->record($definition->version);
            $recorded[] = $definition->version;
        }

        return $recorded;
    }
}
