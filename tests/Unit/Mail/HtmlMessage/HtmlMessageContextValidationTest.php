<?php
declare(strict_types=1);

namespace Test\Unit\Mail\HtmlMessage;

use Mail\MessageFactory\ContextException;
use Mail\MessageFactory\HtmlMessage;
use PHPUnit\Framework\TestCase;

/**
 * Kontrola kontextu šablony před vykreslením: null a prázdné řetězce i ve vnořených polích.
 */
final class HtmlMessageContextValidationTest extends TestCase
{
    public function testNestedEmptyValueIsReportedWithPath(): void
    {
        $this->expectException(ContextException::class);
        $this->expectExceptionMessageMatches("/'requestBody\\.key2' je hodnota typu \\[prazdny_retezec\\]/");
        (new HtmlMessage())->create('/neexistuje.php', ['requestBody' => ['key1' => 'a', 'key2' => ' ']]);
    }

    public function testNullValueIsReported(): void
    {
        $this->expectException(ContextException::class);
        $this->expectExceptionMessageMatches("/'jmeno' je hodnota typu \\[null\\]/");
        (new HtmlMessage())->create('/neexistuje.php', ['jmeno' => null]);
    }
}
