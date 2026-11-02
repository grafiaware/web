<?php

namespace Build\Migration;

use Pes\Database\Handler\Account;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Manipulator\Manipulator;
use Pes\Logger\FileLogger;
use Site\ConfigurationCache;

/**
 * Build DBA Handler + MigrationRunner bez HTTP / UI loginu.
 * Stejné connection klíče jako {@see \Build\Middleware\Build\Controler\MigrationControler}.
 */
final class MigrationConnectionFactory
{
    public function __construct(
        private readonly ?string $projectRoot = null,
        private readonly ?string $logsDirectory = null,
    ) {
    }

    public function createBuildAccount(): Account
    {
        $build = ConfigurationCache::build();

        return new Account(
            (string) $this->cfg('build.db.user.name', $build),
            (string) $this->cfg('build.db.user.password', $build)
        );
    }

    public function createHandler(MigrationTarget $target): Handler
    {
        $account = $this->createBuildAccount();
        $connectionInfo = $this->connectionInfoFor($target);
        $logger = $this->logger();

        $dsnProvider = new DsnProviderMysql();
        $optionsProvider = new OptionsProviderMysql();
        $attributesProvider = new AttributesProvider();
        if (defined('PES_DEVELOPMENT') && PES_DEVELOPMENT) {
            $dsnProvider->setLogger($logger);
            $optionsProvider->setLogger($logger);
            $attributesProvider->setLogger($logger);
        }

        return new Handler(
            $account,
            $connectionInfo,
            $dsnProvider,
            $optionsProvider,
            $attributesProvider,
            $logger
        );
    }

    public function createRunner(MigrationTarget $target): MigrationRunner
    {
        $handler = $this->createHandler($target);
        $manipulator = new Manipulator($handler, $this->logger());
        $pdo = HandlerPdoUnwrapper::unwrap($handler);
        $root = MigrationPaths::defaultRoot($this->projectRoot);

        return new MigrationRunner(
            new MigrationScanner($root),
            new MigrationJournal($pdo),
            new ManipulatorSqlExecutor($manipulator)
        );
    }

    public function createManipulator(MigrationTarget $target): Manipulator
    {
        return new Manipulator($this->createHandler($target), $this->logger());
    }

    public function databaseName(MigrationTarget $target): string
    {
        return match ($target) {
            MigrationTarget::Red => (string) $this->cfg(
                'red.db.connection.name',
                ConfigurationCache::dbUpgrade()
            ),
            MigrationTarget::Auth => (string) $this->cfg(
                'auth.db.connection.name',
                ConfigurationCache::dbOld()
            ),
            MigrationTarget::Events => (string) $this->cfg(
                'dbEvents.db.connection.name',
                ConfigurationCache::dbEvents()
            ),
        };
    }

    private function connectionInfoFor(MigrationTarget $target): ConnectionInfo
    {
        return match ($target) {
            MigrationTarget::Red => new ConnectionInfo(
                $this->cfg('red.db.type', ConfigurationCache::dbUpgrade()),
                $this->cfg('red.db.connection.host', ConfigurationCache::dbUpgrade()),
                $this->cfg('red.db.connection.name', ConfigurationCache::dbUpgrade()),
                $this->cfg('red.db.charset', ConfigurationCache::dbUpgrade()),
                $this->cfg('red.db.collation', ConfigurationCache::dbUpgrade()),
                $this->cfg('red.db.port', ConfigurationCache::dbUpgrade())
            ),
            MigrationTarget::Auth => new ConnectionInfo(
                $this->cfg('auth.db.type', ConfigurationCache::dbOld()),
                $this->cfg('auth.db.connection.host', ConfigurationCache::dbOld()),
                $this->cfg('auth.db.connection.name', ConfigurationCache::dbOld()),
                $this->cfg('auth.db.charset', ConfigurationCache::dbOld()),
                $this->cfg('auth.db.collation', ConfigurationCache::dbOld()),
                $this->cfg('auth.db.port', ConfigurationCache::dbOld())
            ),
            MigrationTarget::Events => new ConnectionInfo(
                $this->cfg('dbEvents.db.type', ConfigurationCache::dbEvents()),
                $this->cfg('dbEvents.db.connection.host', ConfigurationCache::dbEvents()),
                $this->cfg('dbEvents.db.connection.name', ConfigurationCache::dbEvents()),
                $this->cfg('dbEvents.db.charset', ConfigurationCache::dbEvents()),
                $this->cfg('dbEvents.db.collation', ConfigurationCache::dbEvents()),
                $this->cfg('dbEvents.db.port', ConfigurationCache::dbEvents())
            ),
        };
    }

    private function logger(): FileLogger
    {
        $dir = $this->logsDirectory;
        if ($dir === null) {
            $build = ConfigurationCache::build();
            $dir = (string) ($build['build.db.logs.directory'] ?? 'Logs/Build');
        }

        return FileLogger::getInstance($dir, 'MigrationsRebuild.log', FileLogger::REWRITE_LOG);
    }

    /**
     * @param array<string, mixed> $cfg
     */
    private function cfg(string $key, array $cfg): mixed
    {
        if (!array_key_exists($key, $cfg)) {
            throw new \RuntimeException("Chybí konfigurační klíč pro migrace: {$key}");
        }

        return $cfg[$key];
    }
}
