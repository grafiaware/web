<?php

namespace Build\Migration;

use RuntimeException;

/**
 * Read-only drift check — volatelné z bootstrap/middleware.
 * Dev: varování (log). Prod: hard fail při ahead/diverged; behind = fail (pending).
 * Nikdy nespouští drop/rebuild.
 */
final class MigrationDriftGuard
{
    private static bool $ran = false;

    /** @var list<string> */
    private static array $lastMessages = [];

    private const CACHE_TTL_SECONDS = 60;

    /**
     * @param list<MigrationDriftReport> $reports
     * @return list<string> lidské hlášky (dev warn)
     */
    public static function evaluate(array $reports, bool $productionHost): array
    {
        $messages = [];
        foreach ($reports as $report) {
            if ($report->isOk()) {
                continue;
            }
            $msg = 'Migration drift [' . $report->summary() . ']. '
                . 'Opravte: php scripts/db-rebuild.php --target=' . $report->target->value
                . ' --yes  (nebo Build → migrate, pokud jen behind).';
            $messages[] = $msg;

            if ($productionHost) {
                // Prod: ahead i behind = hard fail (forward-only; žádný tichý drop).
                throw new RuntimeException($msg);
            }
        }

        return $messages;
    }

    /**
     * Jednou za PHP proces (+ krátká file cache): zkontroluje targety.
     * Selhání připojení v non-prod jen zaloguje (DB nemusí běžet).
     *
     * @return list<string>
     */
    public static function checkOnce(?MigrationConnectionFactory $factory = null): array
    {
        if (self::$ran) {
            return self::$lastMessages;
        }
        self::$ran = true;

        $cached = self::readCache();
        if ($cached !== null) {
            self::$lastMessages = $cached;

            return self::$lastMessages;
        }

        $productionHost = defined('PES_RUNNING_ON_PRODUCTION_HOST')
            && PES_RUNNING_ON_PRODUCTION_HOST;
        $factory ??= new MigrationConnectionFactory();
        $messages = [];

        try {
            $reports = [];
            foreach (MigrationTarget::all() as $target) {
                $runner = $factory->createRunner($target);
                $reports[] = (new MigrationDriftDetector($runner))->detect($target);
            }
            $messages = self::evaluate($reports, (bool) $productionHost);
            foreach ($messages as $msg) {
                error_log($msg);
            }
            self::writeCache($messages);
        } catch (RuntimeException $e) {
            if ($productionHost) {
                throw $e;
            }
            error_log('Migration drift check skipped/failed: ' . $e->getMessage());
            $messages[] = $e->getMessage();
        } catch (\Throwable $e) {
            if ($productionHost) {
                throw new RuntimeException(
                    'Migration drift check selhal na produkci: ' . $e->getMessage(),
                    0,
                    $e
                );
            }
            error_log('Migration drift check skipped: ' . $e->getMessage());
        }

        self::$lastMessages = $messages;

        return $messages;
    }

    /** Pro unit testy. */
    public static function reset(): void
    {
        self::$ran = false;
        self::$lastMessages = [];
    }

    /**
     * @return list<string>|null
     */
    private static function readCache(): ?array
    {
        $path = self::cachePath();
        if (!is_readable($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }
        $data = json_decode($raw, true);
        if (!is_array($data) || !isset($data['ts'], $data['messages']) || !is_array($data['messages'])) {
            return null;
        }
        if ((time() - (int) $data['ts']) > self::CACHE_TTL_SECONDS) {
            return null;
        }

        /** @var list<string> $messages */
        $messages = array_values(array_map('strval', $data['messages']));

        return $messages;
    }

    /**
     * @param list<string> $messages
     */
    private static function writeCache(array $messages): void
    {
        $path = self::cachePath();
        @file_put_contents(
            $path,
            json_encode(['ts' => time(), 'messages' => $messages], JSON_THROW_ON_ERROR)
        );
    }

    private static function cachePath(): string
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'grafia-migration-drift-cache.json';
    }
}
