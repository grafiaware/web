<?php
declare(strict_types=1);

namespace Test\Support;

use PDO;
use PDOException;
use PHPUnit\Framework\SkippedWithMessageException;
use PHPUnit\Framework\TestCase;

/**
 * Env override pro testovací DB (Docker / CI).
 *
 * Bez TEST_DB_* zůstává chování ConfigurationCache / stávající lokální DB.
 * Když je nastaven TEST_DB_HOST, přepíšou se host/port/jména DB (a volitelně účet).
 */
final class TestDatabaseEnv
{
    public static function isConfigured(): bool
    {
        $host = getenv('TEST_DB_HOST');
        return is_string($host) && $host !== '';
    }

    public static function host(): string
    {
        return self::env('TEST_DB_HOST', '127.0.0.1');
    }

    public static function port(): string
    {
        return self::env('TEST_DB_PORT', '3307');
    }

    public static function user(): ?string
    {
        $user = getenv('TEST_DB_USER');
        return is_string($user) && $user !== '' ? $user : null;
    }

    public static function password(): string
    {
        $password = getenv('TEST_DB_PASSWORD');
        return is_string($password) ? $password : 'webtest';
    }

    public static function redDbName(): string
    {
        return self::env('TEST_DB_RED', 'web_red_test');
    }

    public static function eventsDbName(): string
    {
        return self::env('TEST_DB_EVENTS', 'events_test');
    }

    public static function authDbName(): string
    {
        return self::env('TEST_DB_AUTH', 'auth_test');
    }

    /**
     * Po importu schématu nastavte TEST_DB_SCHEMA_READY=1.
     * Bez toho se při aktivním TEST_DB_HOST integrační DB testy skipnou (prázdný Docker MySQL).
     */
    public static function isSchemaReady(): bool
    {
        if (!self::isConfigured()) {
            return true;
        }
        return getenv('TEST_DB_SCHEMA_READY') === '1';
    }

    /**
     * Parametry pro Red (dbUpgrade / hierarchy).
     *
     * @return array<string, mixed>
     */
    public static function redConnectionParams(): array
    {
        if (!self::isConfigured()) {
            return [];
        }

        $params = [
            'red.db.connection.host' => self::host(),
            'red.db.port' => self::port(),
            'red.db.connection.name' => self::redDbName(),
        ];

        $user = self::user();
        if ($user !== null) {
            $password = self::password();
            $params += [
                'web.db.account.everyone.name' => $user,
                'web.db.account.everyone.password' => $password,
                'web.db.account.authenticated.name' => $user,
                'web.db.account.authenticated.password' => $password,
                'web.db.account.administrator.name' => $user,
                'web.db.account.administrator.password' => $password,
                'red.db.everyone.name' => $user,
                'red.db.everyone.password' => $password,
                'red.db.authenticated.name' => $user,
                'red.db.authenticated.password' => $password,
                'red.db.administrator.name' => $user,
                'red.db.administrator.password' => $password,
            ];
        }

        return $params;
    }

    /**
     * Parametry pro Events DB připojení.
     *
     * @return array<string, mixed>
     */
    public static function eventsConnectionParams(): array
    {
        if (!self::isConfigured()) {
            return [];
        }

        $params = [
            'dbEvents.db.connection.host' => self::host(),
            'dbEvents.db.port' => self::port(),
            'dbEvents.db.connection.name' => self::eventsDbName(),
        ];

        $user = self::user();
        if ($user !== null) {
            $password = self::password();
            $params += [
                'events.db.account.everyone.name' => $user,
                'events.db.account.everyone.password' => $password,
            ];
        }

        return $params;
    }

    /**
     * Parametry pro Auth DB připojení.
     *
     * @return array<string, mixed>
     */
    public static function authConnectionParams(): array
    {
        if (!self::isConfigured()) {
            return [];
        }

        $params = [
            'auth.db.connection.host' => self::host(),
            'auth.db.port' => self::port(),
            'auth.db.connection.name' => self::authDbName(),
        ];

        $user = self::user();
        if ($user !== null) {
            $password = self::password();
            $params += [
                'auth.db.account.everyone.name' => $user,
                'auth.db.account.everyone.password' => $password,
            ];
        }

        return $params;
    }

    public static function canConnect(string $dbName, ?string $user = null, ?string $password = null): bool
    {
        $user ??= self::user() ?? 'webtest';
        $password ??= self::password();
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            self::host(),
            self::port(),
            $dbName
        );

        try {
            $pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 2,
            ]);
            $pdo->query('SELECT 1');
            return true;
        } catch (PDOException) {
            return false;
        }
    }

    /**
     * Skipne test, pokud běžíte v Docker/CI režimu (TEST_DB_HOST) a DB/schéma není připravené.
     * Bez TEST_DB_HOST se nic neděje (lokální DB jako dřív).
     *
     * Použitelné z instance testu ($this) i ze setUpBeforeClass (bez $test → výjimka).
     */
    public static function skipIfTestDatabaseUnavailable(?TestCase $test = null, string $module = 'red'): void
    {
        if (!self::isConfigured()) {
            return;
        }

        $reason = null;

        if (!self::isSchemaReady()) {
            $reason = 'TEST_DB_HOST je nastaven, ale TEST_DB_SCHEMA_READY=1 chybí (importujte schéma testovacích DB).';
        } else {
            [$dbName, $defaultUser, $defaultPassword] = match ($module) {
                'events' => [self::eventsDbName(), 'events_everyone', 'events_everyone'],
                'auth' => [self::authDbName(), 'single_login', 'single_login'],
                default => [self::redDbName(), 'na_admin', 'na_admin'],
            };

            $user = self::user() ?? $defaultUser;
            $password = self::user() !== null ? self::password() : $defaultPassword;

            if (!self::canConnect($dbName, $user, $password)) {
                $reason = sprintf(
                    'Testovací DB %s@%s:%s/%s není dostupná.',
                    $user,
                    self::host(),
                    self::port(),
                    $dbName
                );
            }
        }

        if ($reason === null) {
            return;
        }

        if ($test !== null) {
            $test->markTestSkipped($reason);
        }

        throw new SkippedWithMessageException($reason);
    }

    private static function env(string $name, string $default): string
    {
        $value = getenv($name);
        return is_string($value) && $value !== '' ? $value : $default;
    }
}
