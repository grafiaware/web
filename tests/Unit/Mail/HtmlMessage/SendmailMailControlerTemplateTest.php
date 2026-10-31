<?php
declare(strict_types=1);

namespace Test\Unit\Mail\HtmlMessage;

use Mail\MessageFactory\HtmlMessage;
use PHPUnit\Framework\TestCase;
use Test\Unit\Mail\Support\MailTemplateFixtures;

/**
 * Test C1: MailControler odkazuje na neexistující podekovani-odkazy-igelitka2.php.
 * Test je záměrně psán proti aktuálnímu stavu kódu a očekává se jeho pád/chyba.
 */
final class SendmailMailControlerTemplateTest extends TestCase
{
    public function testPodekovaniOdkazyIgelitka2ReferencedByMailControler(): void
    {
        $this->markTestSkipped('MailControler (kampaň 2021) odkazuje na šablonu podekovani-odkazy-igelitka2.php, která v repu není. Čeká na rozhodnutí: opravit odkaz, nebo starou kampaň odstranit (Z7, #43).');
        $html = (new HtmlMessage())->create(
            MailTemplateFixtures::sendmailControlerMessage('podekovani-odkazy-igelitka2.php'),
            MailTemplateFixtures::podekovaniContext()
        );

        $this->assertStringContainsString('navstevnik.test', $html);
    }
}
