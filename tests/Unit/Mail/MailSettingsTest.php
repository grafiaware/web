<?php
declare(strict_types=1);

namespace Test\Unit\Mail;

use LogicException;
use Mail\MailSettings;
use PHPUnit\Framework\TestCase;

final class MailSettingsTest extends TestCase
{
    public function testDefaultsMatchFormerHardcodedValues(): void
    {
        $s = new MailSettings([]);
        $this->assertSame(['info@najdisi.cz', 'web praci.najdisi.cz'], $s->sender('info'));
        $this->assertSame(['noreply@najdisi.cz', 'web praci.najdisi.cz'], $s->sender('noreply'));
        $this->assertSame([['webmaster@grafia.cz', 'vs']], $s->recipients('registration.bcc'));
    }

    public function testConfigurationOverridesDefaults(): void
    {
        $s = new MailSettings([
            'mail.from' => ['info' => ['veletrh@example.cz', 'Veletrh']],
            'mail.recipients' => ['registration.bcc' => []],
        ]);
        $this->assertSame(['veletrh@example.cz', 'Veletrh'], $s->sender('info'));
        $this->assertSame(['noreply@najdisi.cz', 'web praci.najdisi.cz'], $s->sender('noreply'));
        $this->assertSame([], $s->recipients('registration.bcc'));
    }

    public function testPartyAndRecipients(): void
    {
        $s = new MailSettings([]);
        $party = $s->addRecipients($s->party('noreply'), 'cc', 'exhibitorRegistration.cc');
        $this->assertSame('noreply@najdisi.cz', $party->getFromArray()[0]);
        $this->assertSame(['slehoferova@grafia.cz'], array_column($party->getCcArray(), 0));
    }

    public function testUnknownKeysThrow(): void
    {
        $this->expectException(LogicException::class);
        (new MailSettings([]))->sender('neexistuje');
    }

    public function testLogoDataUri(): void
    {
        $uri = (new MailSettings([]))->logoDataUri('grafia');
        $this->assertStringStartsWith('data:image/gif;base64,R0lGODlh', $uri);
    }

    public function testNajdiSiConfigurationIsComplete(): void
    {
        $s = new MailSettings(\Site\NajdiSi\ConfigurationWeb::mail());
        foreach (['info', 'noreply', 'testmail'] as $key) {
            $this->assertNotSame('', $s->sender($key)[0]);
        }
        foreach (['registration.bcc', 'exhibitorRegistration.to', 'exhibitorRegistration.cc', 'testmail.to'] as $key) {
            $this->assertNotEmpty($s->recipients($key));
        }
    }
}
