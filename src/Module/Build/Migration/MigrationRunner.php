<?php

namespace Build\Migration;

/**
 * Aplikuje pending migrace a umí release baseline (žurnál per DB instance).
 *
 * Žurnál `schema_migrations` žije v cílové databázi — každá DB instance
 * (server / kopie) má vlastní stav. Nepředpokládá se jediný server.
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
     *   unknown: list<string>,
     *   release_baselines: list<string>
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
            if (!$this->isSatisfied($version, $appliedSet)) {
                $pending[] = $version;
            }
        }

        $releaseBaselines = [];
        $unknown = [];
        foreach ($applied as $version) {
            if (ReleaseBaseline::isValid($version)) {
                $releaseBaselines[] = $version;
                continue;
            }
            if (!isset($fileSet[$version])) {
                $unknown[] = $version;
            }
        }

        return [
            'target' => $target->value,
            'applied' => $applied,
            'pending' => $pending,
            'unknown' => $unknown,
            'release_baselines' => $releaseBaselines,
        ];
    }

    /**
     * @return list<string> verze právě aplikované (souborové)
     */
    public function migrate(MigrationTarget $target): array
    {
        $this->journal->ensureTable();
        $appliedSet = array_fill_keys($this->journal->getAppliedVersions(), true);
        $appliedNow = [];
        foreach ($this->scanner->scan($target) as $definition) {
            if ($this->isSatisfied($definition->version, $appliedSet)) {
                continue;
            }
            $this->executor->executeScript($definition->readSql());
            $this->journal->record($definition->version);
            $appliedSet[$definition->version] = true;
            $appliedNow[] = $definition->version;
        }

        return $appliedNow;
    }

    /**
     * Zapíše release baseline (výchozí produkční: {@see ReleaseBaseline::PRODUCTION} = `2025`)
     * bez spuštění SQL. Pokryje všechny souborové migrace s rokem ≤ release.
     *
     * @return list<string> nově zapsané markery (typicky `['2025']`)
     */
    public function baseline(
        MigrationTarget $target,
        string $release = ReleaseBaseline::PRODUCTION
    ): array {
        unset($target); // cíl určuje připojení (žurnál v dané DB); marker je stejný formát
        if (!ReleaseBaseline::isValid($release)) {
            throw new \InvalidArgumentException(
                "Release baseline musí být rok YYYY (např. 2025), dostáno: {$release}"
            );
        }

        $this->journal->ensureTable();
        if ($this->journal->isApplied($release)) {
            return [];
        }
        $this->journal->record($release);

        return [$release];
    }

    /**
     * @param array<string, true> $appliedSet
     */
    private function isSatisfied(string $fileVersion, array $appliedSet): bool
    {
        if (isset($appliedSet[$fileVersion])) {
            return true;
        }

        return ReleaseBaseline::isCoveredByReleaseMarkers($fileVersion, $appliedSet);
    }
}
