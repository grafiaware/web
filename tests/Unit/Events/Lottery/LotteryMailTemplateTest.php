<?php
declare(strict_types=1);

namespace Test\Unit\Events\Lottery;

use Mail\MessageFactory\HtmlMessage;
use PHPUnit\Framework\TestCase;

/**
 * D1: šablona e-mailu po zapojení do losování (QR kód v těle, odkaz na stránku Můj kód, escapování jména).
 */
final class LotteryMailTemplateTest extends TestCase {

    public function testJoinedMail(): void {
        $html = (new HtmlMessage())->create(
            __DIR__ . '/../../../../src/Module/Events/Middleware/Events/Controler/Lottery/Messages/lottery-joined.php',
            [
                'name' => 'Jana <b>Testová</b>',
                'lotteryName' => 'Veletrh 2027 – na místě',
                'qrDataUri' => 'data:image/png;base64,AAAA',
                'myCodeUrl' => 'https://example.test/events/v1/mycode/page',
                'data_logo_grafia' => 'data:image/png;base64,BBBB',
            ]);
        $this->assertStringContainsString('Jana &lt;b&gt;Testová&lt;/b&gt;', $html);
        $this->assertStringContainsString('Veletrh 2027 – na místě', $html);
        $this->assertStringContainsString('data:image/png;base64,AAAA', $html);
        $this->assertStringContainsString('https://example.test/events/v1/mycode/page', $html);
    }
}
