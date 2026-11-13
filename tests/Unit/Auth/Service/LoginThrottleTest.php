<?php
declare(strict_types=1);

namespace Test\Unit\Auth\Service;

use Auth\Service\AccountOrigin;
use Auth\Service\GlobalRoleGrantor;
use Auth\Service\LoginThrottle;
use PHPUnit\Framework\TestCase;

/**
 * A9: výpočet prodlevy zpomaleného přihlašování, typ účtu, konfigurace global_role_grantor (docs/vp/zadani/A9.md).
 */
final class LoginThrottleTest extends TestCase
{
    public function testDelayGrowsAndIsCapped(): void
    {
        $delays = array_map(fn($n) => LoginThrottle::delaySeconds($n, 5, 30, 900), [0, 4, 5, 6, 7, 8, 9, 10, 50]);
        $this->assertSame([0, 0, 30, 60, 120, 240, 480, 900, 900], $delays);
    }

    public function testWaitText(): void
    {
        $this->assertSame('za 30 s', LoginThrottle::waitText(30));
        $this->assertSame('za 1 minutu', LoginThrottle::waitText(60));
        $this->assertSame('za 2 minuty', LoginThrottle::waitText(61));
        $this->assertSame('za 15 minut', LoginThrottle::waitText(900));
    }

    public function testAccountOrigin(): void
    {
        $this->assertSame('admin', AccountOrigin::resolve('admin', 'x@example.test'));
        $this->assertSame('self', AccountOrigin::resolve(null, 'x@example.test'));
        $this->assertSame('legacy', AccountOrigin::resolve(null, ''));
        $this->assertSame('legacy', AccountOrigin::resolve('nesmysl', null));
    }

    public function testGlobalRoleGrantorConfig(): void
    {
        $hash = password_hash('heslo-pro-test-123', PASSWORD_DEFAULT);
        $g = new GlobalRoleGrantor(['enabled' => true, 'loginName' => 'global_role_grantor', 'passwordHash' => $hash], true);
        $this->assertTrue($g->isActive());
        $this->assertTrue($g->matches(' Global_Role_Grantor '));
        $this->assertTrue($g->verify('heslo-pro-test-123'));
        $this->assertFalse($g->verify('jine'));
        $this->assertTrue($g->loginAggregate()->isPersisted());
        $this->assertSame('global_role_grantor', $g->loginAggregate()->getCredentials()->getRoleFk());
        $this->assertFalse((new GlobalRoleGrantor(['enabled' => true, 'passwordHash' => ''], true))->isActive(), 'bez hashe neexistuje');
        $this->assertFalse((new GlobalRoleGrantor(['enabled' => 'true', 'passwordHash' => $hash], true))->isActive(), 'enabled musí být true');
        $this->assertNull(GlobalRoleGrantor::loadConfigFile('/neexistuje/ConfigurationRoot.local.php'));
        $this->assertFalse($g->matches('jiny_ucet'));
    }
}
