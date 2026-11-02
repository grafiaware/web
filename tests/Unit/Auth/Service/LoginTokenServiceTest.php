<?php
declare(strict_types=1);

namespace Test\Unit\Auth\Service;

use Auth\Model\Dao\LoginTokenDao;
use Auth\Service\LoginTokenService;
use Pes\Model\RowData\RowData;
use PHPUnit\Framework\TestCase;

final class LoginTokenServiceTest extends TestCase
{
    /** @var array<string, array> */
    private array $rows = [];
    private \DateTimeImmutable $now;

    private function service(): LoginTokenService
    {
        $dao = $this->createStub(LoginTokenDao::class);
        $dao->method('insert')->willReturnCallback(function ($row) {
            $data = $row->yieldChangedAsArrayObject()->getArrayCopy() + ['used' => null];
            $this->rows[$data['token_hash']] = $data;
            return true;
        });
        $dao->method('get')->willReturnCallback(function (array $key) {
            if (!isset($this->rows[$key['token_hash']])) {
                return null;
            }
            return new RowData($this->rows[$key['token_hash']]);
        });
        $dao->method('markUsed')->willReturnCallback(function (string $hash, string $purpose, \DateTimeInterface $now) {
            $r = $this->rows[$hash] ?? null;
            if (!$r || $r['used'] !== null || $r['purpose'] !== $purpose || $r['expires'] <= $now->format('Y-m-d H:i:s')) {
                return false;
            }
            $this->rows[$hash]['used'] = $now->format('Y-m-d H:i:s');
            return true;
        });
        $dao->method('countCreatedSince')->willReturnCallback(function (string $login, string $purpose, \DateTimeInterface $since) {
            return count(array_filter($this->rows, fn($r) => $r['login_name_fk'] === $login && $r['purpose'] === $purpose
                && $r['created'] >= $since->format('Y-m-d H:i:s')));
        });
        return new LoginTokenService($dao, fn() => $this->now);
    }

    protected function setUp(): void
    {
        $this->rows = [];
        $this->now = new \DateTimeImmutable('2026-11-02 10:00:00');
    }

    public function testTokenIsStoredOnlyAsHash(): void
    {
        $token = $this->service()->issue('jan_novak', LoginTokenService::PURPOSE_LOGIN, 30);
        $this->assertTrue(LoginTokenService::isWellFormed($token));
        $this->assertArrayHasKey(hash('sha256', $token), $this->rows);
        foreach ($this->rows as $row) {
            $this->assertNotContains($token, $row);
        }
    }

    public function testConsumeOnlyOnce(): void
    {
        $service = $this->service();
        $token = $service->issue('jan_novak', LoginTokenService::PURPOSE_LOGIN, 30);
        $this->assertSame('jan_novak', $service->peek($token, LoginTokenService::PURPOSE_LOGIN));
        $this->assertSame('jan_novak', $service->consume($token, LoginTokenService::PURPOSE_LOGIN));
        $this->assertNull($service->consume($token, LoginTokenService::PURPOSE_LOGIN));
        $this->assertNull($service->peek($token, LoginTokenService::PURPOSE_LOGIN));
    }

    public function testExpiredTokenIsRejected(): void
    {
        $service = $this->service();
        $token = $service->issue('jan_novak', LoginTokenService::PURPOSE_LOGIN, 30);
        $this->now = $this->now->modify('+31 minutes');
        $this->assertNull($service->peek($token, LoginTokenService::PURPOSE_LOGIN));
        $this->assertNull($service->consume($token, LoginTokenService::PURPOSE_LOGIN));
    }

    public function testWrongPurposeAndGarbageAreRejected(): void
    {
        $service = $this->service();
        $token = $service->issue('jan_novak', LoginTokenService::PURPOSE_SET_PASSWORD, 60);
        $this->assertNull($service->consume($token, LoginTokenService::PURPOSE_LOGIN));
        $this->assertNull($service->consume('nesmysl', LoginTokenService::PURPOSE_LOGIN));
        $this->assertNull($service->consume(str_repeat('a', 43), LoginTokenService::PURPOSE_LOGIN));
    }

    public function testRateLimit(): void
    {
        $service = $this->service();
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($service->canIssue('jan_novak', LoginTokenService::PURPOSE_LOGIN, 3, 15));
            $service->issue('jan_novak', LoginTokenService::PURPOSE_LOGIN, 30);
        }
        $this->assertFalse($service->canIssue('jan_novak', LoginTokenService::PURPOSE_LOGIN, 3, 15));
        $this->assertTrue($service->canIssue('jina_osoba', LoginTokenService::PURPOSE_LOGIN, 3, 15));
        $this->now = $this->now->modify('+16 minutes');
        $this->assertTrue($service->canIssue('jan_novak', LoginTokenService::PURPOSE_LOGIN, 3, 15));
    }
}
