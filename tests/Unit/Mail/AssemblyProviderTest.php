<?php
declare(strict_types=1);

namespace Test\Unit\Mail;

use Mail\MessageFactory\HtmlMessage;
use PHPUnit\Framework\TestCase;
use Sendmail\Middleware\Sendmail\Campaign\AssemblyProvider\AssemblyProvider;
use Sendmail\Middleware\Sendmail\Campaign\AssemblyProvider\AssemblyProviderInterface;
use UnexpectedValueException;

final class AssemblyProviderTest extends TestCase
{
    private AssemblyProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new AssemblyProvider(new HtmlMessage());
    }

    public function testGetAssemblyAnketa2025(): void
    {
        $assembly = $this->provider->getAssembly(
            AssemblyProviderInterface::ASSEMBLY_ANKETA_2025,
            'adresat@example.cz',
            'Jan Novak'
        );

        $this->assertSame('Veletrh práce a vzdělávání - nabídka', $assembly->getContent()->getSubjectRaw());
        $this->assertStringContainsString('Veletrh práce a vzdělávání', $assembly->getContent()->getHtml());
        $this->assertSame(['info@najdisi.cz', 'Veletrh Práce'], $assembly->getParty()->getFromArray());
        $this->assertSame([['adresat@example.cz', 'Jan Novak']], $assembly->getParty()->getToArray());
    }

    public function testThrowsOnUnknownAssembly(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->provider->getAssembly('neznama-sestava', 'a@b.cz', 'Test');
    }
}
