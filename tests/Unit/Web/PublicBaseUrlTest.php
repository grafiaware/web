<?php
declare(strict_types=1);

namespace Test\Unit\Web;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Web\Service\PublicBaseUrl;

final class PublicBaseUrlTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv('WEB_PUBLIC_HOST');
        putenv('WEB_PUBLIC_SCHEME');
        putenv('WEB_DEV_HOSTS');
        unset($_SERVER['WEB_PUBLIC_HOST'], $_SERVER['WEB_PUBLIC_SCHEME'], $_SERVER['WEB_DEV_HOSTS']);
        $ref = new \ReflectionClass(PublicBaseUrl::class);
        $prop = $ref->getProperty('fileEnv');
        $prop->setAccessible(true);
        $prop->setValue(null, null);
        parent::tearDown();
    }

    public function testUsesRequestHostWhenPublicHostEmpty(): void
    {
        putenv('WEB_PUBLIC_HOST=');
        $request = $this->request('http', 'localhost:8080');
        $url = PublicBaseUrl::absolute($request, '/', 'events/v1/scan/L1');
        $this->assertSame('http://localhost:8080/events/v1/scan/L1', $url);
    }

    public function testOverridesHostFromEnv(): void
    {
        putenv('WEB_PUBLIC_HOST=ps.dev.local');
        putenv('WEB_PUBLIC_SCHEME=https');
        $request = $this->request('http', 'localhost:8080');
        $url = PublicBaseUrl::absolute($request, '/', 'events/v1/scan/L1');
        $this->assertSame('https://ps.dev.local/events/v1/scan/L1', $url);
    }

    public function testDefaultDevHosts(): void
    {
        putenv('WEB_DEV_HOSTS=');
        $this->assertSame(
            ['ps.dev.local', 'vs.dev.local', 'ks.dev.local'],
            PublicBaseUrl::devHosts()
        );
    }

    public function testCustomDevHosts(): void
    {
        putenv('WEB_DEV_HOSTS=ps.dev.local, vs.dev.local ,ks.dev.local');
        $this->assertSame(
            ['ps.dev.local', 'vs.dev.local', 'ks.dev.local'],
            PublicBaseUrl::devHosts()
        );
    }

    private function request(string $scheme, string $host): ServerRequestInterface
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getScheme')->willReturn($scheme);
        $uri->method('getHost')->willReturn(explode(':', $host)[0]);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);
        $request->method('getServerParams')->willReturn(['HTTP_HOST' => $host]);
        return $request;
    }
}
