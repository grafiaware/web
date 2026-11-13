<?php

declare(strict_types=1);

namespace Test\Unit\StaticRegistry;

use PHPUnit\Framework\TestCase;
use StaticRegistry\Service\StaticTemplateLocation;

final class StaticTemplateLocationTest extends TestCase
{
    public function testSplitsParentPathAndLeafTemplate(): void
    {
        $this->assertSame(
            ['path' => 'events/', 'template' => 'profil-firmy'],
            StaticTemplateLocation::fromDirectory('events/profil-firmy/')
        );
        $this->assertSame(
            ['path' => 'events/admin/', 'template' => 'admin-company'],
            StaticTemplateLocation::fromDirectory('events\\admin\\admin-company')
        );
    }

    public function testTemplateDirectlyUnderStaticHasEmptyPath(): void
    {
        $this->assertSame(
            ['path' => '', 'template' => 'zkouska-mailu'],
            StaticTemplateLocation::fromDirectory('zkouska-mailu')
        );
    }

    public function testEncodeRoundTrip(): void
    {
        $encoded = StaticTemplateLocation::encode('auth/admin/', 'admin-credentials');
        $this->assertSame(
            ['path' => 'auth/admin/', 'template' => 'admin-credentials'],
            StaticTemplateLocation::decode($encoded)
        );
        $this->assertNull(StaticTemplateLocation::decode('not-json'));
        $this->assertNull(StaticTemplateLocation::decode('{"path":"events/"}'));
    }
}
