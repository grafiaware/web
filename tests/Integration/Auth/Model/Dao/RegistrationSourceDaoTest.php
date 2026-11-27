<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Model\Dao;

use Auth\Service\RegistrationSourceService;
use Auth\Service\Source\VisitSource;
use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;
use DateTimeImmutable;
use Pes\Container\Container;
use Test\AppRunner\AppRunner;

/**
 * F1 (#47): zdroj registrace v DB auth – zápis (první zdroj platí), souhrn podle zdroje s potvrzenými (credentials),
 * kaskádové smazání s loginem.
 */
final class RegistrationSourceDaoTest extends AppRunner
{
    private const PREFIX = 'f1src_test_';

    private static Container $c;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        self::$c = (new AuthContainerConfigurator())->configure((new AuthDbContainerConfigurator())->configure(new Container()));
        self::cleanup();
    }

    public static function tearDownAfterClass(): void
    {
        self::cleanup();
    }

    private static function exec(string $sql, array $params = []): void
    {
        $stmt = self::$c->get(\Pes\Database\Handler\Handler::class)->prepare($sql);
        $stmt->execute($params);
    }

    private static function cleanup(): void
    {
        self::exec("DELETE FROM credentials WHERE login_name_fk LIKE :p", ['p' => self::PREFIX . '%']);
        self::exec("DELETE FROM login WHERE login_name LIKE :p", ['p' => self::PREFIX . '%']);   // kaskáda: registration_source
    }

    public function testRecordAndCounts(): void
    {
        $service = self::$c->get(RegistrationSourceService::class);
        $now = new DateTimeImmutable('2026-11-24 10:00:00');
        $code = 'f1src-' . substr(uniqid(), -6);
        foreach (['a', 'b', 'c'] as $i => $n) {
            self::exec("INSERT INTO login (login_name) VALUES (:n)", ['n' => self::PREFIX . $n]);
        }
        $service->record(self::PREFIX . 'a', VisitSource::fromLink($code, 'fb', $now));
        $service->record(self::PREFIX . 'a', VisitSource::fromLink($code, 'ig', $now));   // první zdroj platí
        $service->record(self::PREFIX . 'b', VisitSource::fromLink($code, 'fb', $now));
        $service->record(self::PREFIX . 'c', VisitSource::fromUtm(['utm_source' => 'f1src-test', 'utm_campaign' => 'k'], $now));
        self::exec("INSERT INTO credentials (login_name_fk, password_hash) VALUES (:n, 'x')", ['n' => self::PREFIX . 'b']);

        $rows = array_values(array_filter($service->counts(), fn($r) => $r['link_code'] === $code || $r['utm_source'] === 'f1src-test'));
        usort($rows, fn($x, $y) => strcmp((string) $x['link_code'], (string) $y['link_code']));
        $this->assertCount(2, $rows);
        $this->assertNull($rows[0]['link_code']);
        $this->assertSame(1, $rows[0]['registered']);
        $this->assertSame('fb', $rows[1]['channel']);
        $this->assertSame(2, $rows[1]['registered']);
        $this->assertSame(1, $rows[1]['confirmed']);

        $this->assertSame([], array_filter($service->counts('2099-01-01 00:00:00', null), fn($r) => $r['link_code'] === $code));

        self::exec("DELETE FROM login WHERE login_name = :n", ['n' => self::PREFIX . 'a']);
        $left = array_filter($service->counts(), fn($r) => $r['link_code'] === $code);
        $this->assertSame(1, array_values($left)[0]['registered']);
    }
}
