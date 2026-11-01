<?php
declare(strict_types=1);

namespace Test\Unit\Mail\HtmlMessage;

use Mail\MessageFactory\HtmlMessage;
use PHPUnit\Framework\TestCase;
use Test\Unit\Mail\Support\MailTemplateFixtures;

/**
 * Šablona, kterou používá MailControler (kampaň „Poděkování, odkazy a virtuální igelitka“).
 */
final class SendmailMailControlerTemplateTest extends TestCase
{
    public function testPodekovaniOdkazyIgelitkaReferencedByMailControler(): void
    {
        $html = (new HtmlMessage())->create(
            MailTemplateFixtures::sendmailControlerMessage('podekovani-odkazy-igelitka.php'),
            MailTemplateFixtures::podekovaniContext()
        );

        $this->assertStringContainsString('navstevnik.test', $html);
    }
}
