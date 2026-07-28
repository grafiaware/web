<?php
declare(strict_types=1);

namespace Test\Unit\Mail\HtmlMessage;

use Mail\MessageFactory\HtmlMessage;
use PHPUnit\Framework\TestCase;
use Test\Unit\Mail\Support\MailTemplateFixtures;

final class AuthMailTemplateTest extends TestCase
{
    private HtmlMessage $factory;

    protected function setUp(): void
    {
        $this->factory = new HtmlMessage();
    }

    public function testRegistrationContainsConfirmationUrl(): void
    {
        $html = $this->factory->create(
            MailTemplateFixtures::authMessage('registration.php'),
            MailTemplateFixtures::registrationContext()
        );

        $this->assertStringContainsString('<!DOCTYPE html>', $html);
        $this->assertStringContainsString('https://test.example/auth/v1/confirm/abc123', $html);
        $this->assertStringContainsString('Potvrďte registraci', $html);
        $this->assertStringContainsString('Děkujeme za Vaši registraci', $html);
    }

    public function testConfirmCompleted(): void
    {
        $html = $this->factory->create(
            MailTemplateFixtures::authMessage('confirm.php'),
            MailTemplateFixtures::confirmContext()
        );

        $this->assertStringContainsString('registrace byla dokončena', $html);
        $this->assertStringContainsString('Grafia', $html);
    }

    public function testConfirmRepre1(): void
    {
        $html = $this->factory->create(
            MailTemplateFixtures::authMessage('confirmRepre1.php'),
            MailTemplateFixtures::confirmContext()
        );

        $this->assertStringContainsString('zástupce firmy', $html);
        $this->assertStringContainsString('interním ověřovacím procesem', $html);
    }

    public function testRegistrationExhib(): void
    {
        $html = $this->factory->create(
            MailTemplateFixtures::authMessage('registrationexhib.php'),
            MailTemplateFixtures::registrationExhibContext()
        );

        $this->assertStringContainsString('jan.novak', $html);
        $this->assertStringContainsString('jan@example.cz', $html);
        $this->assertStringContainsString('Grafia s.r.o.', $html);
        $this->assertStringContainsString('vystavovatel', $html);
    }

    public function testForgottenPassword(): void
    {
        $html = $this->factory->create(
            MailTemplateFixtures::authMessage('forgottenpassword.php'),
            MailTemplateFixtures::forgottenPasswordContext()
        );

        $this->assertStringContainsString('jan.novak', $html);
        $this->assertStringContainsString('NoveHeslo456', $html);
        $this->assertStringContainsString('nového hesla', $html);
    }

    public function testConfirmRepre2(): void
    {
        $html = $this->factory->create(
            MailTemplateFixtures::authMessage('confirmRepre2.php'),
            MailTemplateFixtures::confirmRepre2Context()
        );

        $this->assertStringContainsString('Grafia s.r.o.', $html);
        $this->assertStringContainsString('zástupcem firmy', $html);
    }

    public function testTestmail(): void
    {
        $html = $this->factory->create(
            MailTemplateFixtures::authMessage('testmail.php'),
            MailTemplateFixtures::testmailContext()
        );

        $this->assertStringContainsString('https://test.example/confirm', $html);
        $this->assertStringContainsString('alpha', $html);
        $this->assertStringContainsString('beta', $html);
    }
}
