<?php
declare(strict_types=1);

namespace Test\Unit\Status;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Status\Session\SessionUnlockPolicy;

final class SessionUnlockPolicyTest extends TestCase
{
    private SessionUnlockPolicy $policy;

    protected function setUp(): void
    {
        $this->policy = new SessionUnlockPolicy();
    }

    public function testIsCascadeGetRequiresGetAndHeader(): void
    {
        $this->assertTrue(
            $this->policy->isCascadeGet($this->request('GET', '/page', [SessionUnlockPolicy::CASCADE_HEADER => '1']))
        );
        $this->assertFalse(
            $this->policy->isCascadeGet($this->request('POST', '/page', [SessionUnlockPolicy::CASCADE_HEADER => '1']))
        );
        $this->assertFalse(
            $this->policy->isCascadeGet($this->request('GET', '/page', []))
        );
    }

    public function testShouldFinishIsFalseForNewSessionCascade(): void
    {
        $request = $this->request('GET', '/page', [SessionUnlockPolicy::CASCADE_HEADER => '1']);

        $this->assertTrue($this->policy->isNewSessionCascadeAnomaly($request, true));
        $this->assertFalse($this->policy->shouldFinish($request, true));
    }

    public function testShouldFinishIsTrueForExistingSessionCascade(): void
    {
        $request = $this->request('GET', '/page', [SessionUnlockPolicy::CASCADE_HEADER => '1']);

        $this->assertTrue($this->policy->shouldFinish($request, false));
    }

    public function testNeedsReopenForFlashAndPresentedDriverPaths(): void
    {
        $headers = [SessionUnlockPolicy::CASCADE_HEADER => '1'];

        $this->assertTrue(
            $this->policy->needsReopen($this->request('GET', '/web/v1/component/flash', $headers))
        );
        $this->assertTrue(
            $this->policy->needsReopen($this->request('GET', '/web/v1/flash', $headers))
        );
        $this->assertTrue(
            $this->policy->needsReopen($this->request('GET', '/red/v1/presenteddriver/abc', $headers))
        );
        $this->assertFalse(
            $this->policy->needsReopen($this->request('GET', '/web/v1/page/home', $headers))
        );
    }

    public function testShouldRecordLastGetOnlyForPlainGet(): void
    {
        $this->assertTrue($this->policy->shouldRecordLastGet($this->request('GET', '/home', [])));
        $this->assertFalse(
            $this->policy->shouldRecordLastGet(
                $this->request('GET', '/home', [SessionUnlockPolicy::CASCADE_HEADER => '1'])
            )
        );
        $this->assertFalse($this->policy->shouldRecordLastGet($this->request('POST', '/home', [])));
    }

    public function testPresentedDriverUri(): void
    {
        $this->assertSame(
            'red/v1/presenteddriver/menu-42',
            SessionUnlockPolicy::presentedDriverUri('menu-42')
        );
    }

    private function request(string $method, string $path, array $headers): ServerRequestInterface
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn($path);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn($method);
        $request->method('getUri')->willReturn($uri);
        $request->method('hasHeader')->willReturnCallback(
            static fn(string $name): bool => array_key_exists($name, $headers)
        );

        return $request;
    }
}
