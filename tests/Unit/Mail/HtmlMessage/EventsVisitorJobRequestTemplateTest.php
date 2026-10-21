<?php
declare(strict_types=1);

namespace Test\Unit\Mail\HtmlMessage;

use Mail\MessageFactory\HtmlMessage;
use PHPUnit\Framework\TestCase;
use Test\Unit\Mail\Support\MailTemplateFixtures;

final class EventsVisitorJobRequestTemplateTest extends TestCase
{
    public function testPracovniUdajeNavstevnika(): void
    {
        $html = (new HtmlMessage())->create(
            MailTemplateFixtures::eventsMessage('pracovni-udaje-navstevnika.php'),
            MailTemplateFixtures::visitorJobRequestContext()
        );

        $this->assertStringContainsString('Programator PHP', $html);
        $this->assertStringContainsString('jan@example.cz', $html);
        $this->assertStringContainsString('123456789', $html);
        $this->assertStringContainsString('VS informatika', $html);
        $this->assertStringContainsString('PHP, SQL', $html);
        $this->assertStringContainsString('Ing.', $html);
        $this->assertStringContainsString('Novak', $html);
    }
}
