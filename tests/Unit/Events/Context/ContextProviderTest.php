<?php
declare(strict_types=1);

namespace Test\Unit\Events\Context;

use Access\Enum\RoleEnum;
use Events\Model\Context\ContextProvider;
use PHPUnit\Framework\TestCase;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Entity\SecurityInterface;
use Auth\Model\Entity\LoginAggregateFullInterface;
use Auth\Model\Entity\CredentialsInterface;

/**
 * G0 (test 27. 11.): nezveřejněné eventy vidí (a upraví) jen events_administrator.
 */
final class ContextProviderTest extends TestCase {

    private function repo(?string $role): StatusSecurityRepo {
        $credentials = $this->createMock(CredentialsInterface::class);
        $credentials->method('getRoleFk')->willReturn($role);
        $login = $this->createMock(LoginAggregateFullInterface::class);
        $login->method('getCredentials')->willReturn($credentials);
        $security = $this->createMock(SecurityInterface::class);
        $security->method('getLoginAggregate')->willReturn($role === null ? null : $login);
        $repo = $this->createMock(StatusSecurityRepo::class);
        $repo->method('get')->willReturn($security);
        return $repo;
    }

    public function testAdministratorSeesUnpublished(): void {
        $this->assertFalse((new ContextProvider($this->repo(RoleEnum::EVENTS_ADMINISTRATOR)))->showOnlyPublished());
    }

    public function testOthersSeeOnlyPublished(): void {
        $this->assertTrue((new ContextProvider($this->repo(RoleEnum::VISITOR)))->showOnlyPublished());
        $this->assertTrue((new ContextProvider($this->repo(RoleEnum::REPRESENTATIVE)))->showOnlyPublished());
        $this->assertTrue((new ContextProvider($this->repo(null)))->showOnlyPublished(), 'nepřihlášený');
        $this->assertTrue((new ContextProvider())->showOnlyPublished(), 'bez repo – dosavadní chování');
    }

    public function testForce(): void {
        $provider = new ContextProvider($this->repo(RoleEnum::EVENTS_ADMINISTRATOR));
        $provider->forceShowOnlyPublished(true);
        $this->assertTrue($provider->showOnlyPublished());
        $provider->forceShowOnlyPublished(null);
        $this->assertFalse($provider->showOnlyPublished());
    }
}
