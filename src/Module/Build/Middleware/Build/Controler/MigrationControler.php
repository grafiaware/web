<?php

namespace Build\Middleware\Build\Controler;

use Build\Migration\ManipulatorSqlExecutor;
use Build\Migration\MigrationJournal;
use Build\Migration\MigrationPaths;
use Build\Migration\MigrationRunner;
use Build\Migration\MigrationScanner;
use Build\Migration\MigrationTarget;
use Pes\Database\Handler\Account;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Manipulator\Manipulator;
use Pes\Logger\FileLogger;
use Psr\Http\Message\ServerRequestInterface;
use Site\ConfigurationCache;

/**
 * Spouštění verzovaných SQL migrací z Build panelu.
 */
class MigrationControler extends BuildControlerAbstract
{
    public function status(ServerRequestInterface $request)
    {
        $target = $this->resolveTarget($request);
        $runner = $this->createRunner($target);
        $status = $runner->status($target);

        $html = [];
        $html[] = '<h3>Stav migrací — ' . htmlspecialchars($target->value) . '</h3>';
        $html[] = '<p>Kořen: <code>' . htmlspecialchars(MigrationPaths::defaultRoot()) . '/' . $target->value . '</code></p>';
        $html[] = $this->renderVersionList('Aplikované', $status['applied']);
        $html[] = $this->renderVersionList('Čekající (pending)', $status['pending']);
        $html[] = $this->renderVersionList('V žurnálu bez souboru (unknown)', $status['unknown']);

        return $this->createHtmlReportResponse(implode(PHP_EOL, $html));
    }

    public function migrate(ServerRequestInterface $request)
    {
        $target = $this->resolveTarget($request);
        $runner = $this->createRunner($target);
        $applied = $runner->migrate($target);

        $html = [];
        $html[] = '<h3>Migrace dokončena — ' . htmlspecialchars($target->value) . '</h3>';
        if ($applied === []) {
            $html[] = '<p>Žádné pending migrace.</p>';
        } else {
            $html[] = $this->renderVersionList('Nově aplikované', $applied);
        }
        $status = $runner->status($target);
        $html[] = $this->renderVersionList('Stále pending', $status['pending']);

        return $this->createHtmlReportResponse(implode(PHP_EOL, $html));
    }

    public function baseline(ServerRequestInterface $request)
    {
        $target = $this->resolveTarget($request);
        $runner = $this->createRunner($target);
        $recorded = $runner->baseline($target);

        $html = [];
        $html[] = '<h3>Baseline — ' . htmlspecialchars($target->value) . '</h3>';
        $html[] = '<p>Verze zapsány do <code>schema_migrations</code> bez spuštění SQL.</p>';
        if ($recorded === []) {
            $html[] = '<p>Nic nového k zápisu (vše už bylo v žurnálu).</p>';
        } else {
            $html[] = $this->renderVersionList('Nově zapsané', $recorded);
        }

        return $this->createHtmlReportResponse(implode(PHP_EOL, $html));
    }

    private function resolveTarget(ServerRequestInterface $request): MigrationTarget
    {
        $body = $request->getParsedBody();
        $raw = is_array($body) ? ($body['target'] ?? 'red') : 'red';
        $target = MigrationTarget::tryFromString(is_string($raw) ? $raw : 'red');
        if ($target === null) {
            throw new \InvalidArgumentException(
                'Neplatný target migrace. Povolené: red, auth, events.'
            );
        }

        return $target;
    }

    private function createRunner(MigrationTarget $target): MigrationRunner
    {
        $handler = $this->createHandler($target);
        $logger = FileLogger::getInstance(
            $this->container->get('build.db.logs.directory'),
            'Migrations.log',
            FileLogger::REWRITE_LOG
        );
        $manipulator = new Manipulator($handler, $logger);
        $this->manipulator = $manipulator;

        if (!$handler instanceof \PDO) {
            throw new \RuntimeException('Build Handler musí být PDO pro MigrationJournal.');
        }

        return new MigrationRunner(
            new MigrationScanner(MigrationPaths::defaultRoot()),
            new MigrationJournal($handler),
            new ManipulatorSqlExecutor($manipulator)
        );
    }

    private function createHandler(MigrationTarget $target): Handler
    {
        $account = $this->container->get(Account::class);
        $connectionInfo = $this->connectionInfoFor($target);
        $logger = FileLogger::getInstance(
            $this->container->get('build.db.logs.directory'),
            'Migrations.log',
            FileLogger::REWRITE_LOG
        );

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

    /**
     * @param list<string> $versions
     */
    private function renderVersionList(string $title, array $versions): string
    {
        $items = $versions === []
            ? '<li><em>(prázdné)</em></li>'
            : implode('', array_map(
                static fn (string $v): string => '<li><code>' . htmlspecialchars($v) . '</code></li>',
                $versions
            ));

        return '<h4>' . htmlspecialchars($title) . ' (' . count($versions) . ')</h4><ul>' . $items . '</ul>';
    }
}
