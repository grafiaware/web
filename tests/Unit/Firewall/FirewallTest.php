<?php
declare(strict_types=1);

namespace Test\Unit\Firewall;

use Firewall\Middleware\Firewall;
use Firewall\Middleware\Rule\RoleInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class FirewallTest extends TestCase
{
    public function testGrantedRequestIsPassedToHandler(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn($response);

        $accessor = $this->createMock(RoleInterface::class);
        $accessor->method('granted')->willReturn(true);

        $firewall = new Firewall($accessor);
        $result = $firewall->process($this->createMock(ServerRequestInterface::class), $handler);

        $this->assertSame($response, $result);
    }

    public function testDeniedRequestReturns403WithMessage(): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $accessor = $this->createMock(RoleInterface::class);
        $accessor->method('granted')->willReturn(false);
        $accessor->method('restrictMessage')->willReturn('Zakázáno');

        $firewall = new Firewall($accessor);
        $response = $firewall->process($this->createMock(ServerRequestInterface::class), $handler);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('Zakázáno', (string) $response->getBody());
    }
}
