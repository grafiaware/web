<?php
declare(strict_types=1);

namespace Test\Unit\Mail\HtmlMessage;

use Mail\MessageFactory\HtmlMessage;
use PHPUnit\Framework\TestCase;
use Test\Unit\Mail\Support\MailTemplateFixtures;

final class SendmailCampaignTemplateTest extends TestCase
{
    public function testAnketa2025(): void
    {
        $html = (new HtmlMessage())->create(
            MailTemplateFixtures::sendmailAssemblyMessage('Pro ankety 2025.php'),
            MailTemplateFixtures::anketa2025Context()
        );

        $this->assertStringContainsString('Veletrh práce a vzdělávání', $html);
        $this->assertStringContainsString('anketní lístek', $html);
        $this->assertStringContainsString('Dobrý den', $html);
    }
}
