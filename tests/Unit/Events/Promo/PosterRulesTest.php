<?php
declare(strict_types=1);

namespace Test\Unit\Events\Promo;

use Events\Service\Promo\PosterDataService;
use Events\Service\Promo\PromoConfig;
use Events\Service\Promo\SvgQrSanitizer;
use PHPUnit\Framework\TestCase;

/**
 * F3 (#47): vyčištění SVG z QRdirectoru, adresa přečtená z QR, kontrola veřejné adresy, adresa firmy na plakátu,
 * výchozí text plakátu.
 */
final class PosterRulesTest extends TestCase {

    private const QR = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 29 29"><rect width="29" height="29" fill="#fff"/>'
        . '<path d="M0 0h7v7H0z M8 0h1v1H8z" fill="#000"/></svg>';

    public function testCleanQrKeepsShapesAndColors(): void {
        $r = SvgQrSanitizer::sanitize('<?xml version="1.0" encoding="UTF-8"?>' . self::QR);
        $this->assertNull($r['error']);
        $this->assertSame(2, $r['shapes']);
        $this->assertStringContainsString('viewBox="0 0 29 29"', $r['svg']);
        $this->assertStringContainsString('fill="#fff"', $r['svg']);
        $this->assertStringContainsString('d="M0 0h7v7H0z M8 0h1v1H8z"', $r['svg']);
    }

    public function testDangerousContentRemoved(): void {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 10 10" onload="alert(1)">'
            . '<script>alert(1)</script><style>@import url(http://x/a.css); .d{fill:#000}</style>'
            . '<a xlink:href="javascript:alert(1)"><rect width="1" height="1"/></a>'
            . '<foreignObject><div xmlns="http://www.w3.org/1999/xhtml">x</div></foreignObject>'
            . '<image href="http://x/a.png" width="1" height="1"/><use href="#m"/>'
            . '<rect class="d" x="1" y="1" width="1" height="1" onclick="x()" style="fill:url(#g)"/>'
            . '<animate attributeName="fill" to="red"/><g><rect x="2" y="2" width="1" height="1" fill="javascript:x"/></g></svg>';
        $r = SvgQrSanitizer::sanitize($svg);
        $this->assertNull($r['error']);
        foreach (['script', 'style', 'onload', 'onclick', 'javascript', 'href', 'foreignObject', 'image', 'use', 'animate', 'url(', '<a'] as $bad) {
            $this->assertStringNotContainsStringIgnoringCase($bad, $r['svg'], $bad);
        }
        $this->assertSame(2, $r['shapes']);                      // rect.d a rect v g; rect v <a> pryč
        $this->assertStringContainsString('fill="#000"', $r['svg']);   // barva z třídy; style s url() zahozen
    }

    public function testClassAndInlineStyleColorsBecomeAttributes(): void {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px"><style>.bg,.x{fill:#ffffff}.fg{fill:#111 !important}</style>'
            . '<rect class="bg" width="20" height="20"/><path class="fg" d="M0 0h5v5H0z" style="fill-rule:evenodd"/></svg>';
        $r = SvgQrSanitizer::sanitize($svg);
        $this->assertNull($r['error']);
        $this->assertStringContainsString('viewBox="0 0 20 20"', $r['svg']);
        $this->assertStringContainsString('<rect width="20" height="20" fill="#ffffff"/>', $r['svg']);
        $this->assertStringContainsString('fill="#111"', $r['svg']);
        $this->assertStringContainsString('fill-rule="evenodd"', $r['svg']);
    }

    public function testRejected(): void {
        $this->assertSame('Soubor není SVG.', SvgQrSanitizer::sanitize("\x89PNG\r\n")['error']);
        $this->assertStringContainsString('DTD', (string) SvgQrSanitizer::sanitize(
            '<!DOCTYPE svg [<!ENTITY x SYSTEM "file:///etc/passwd">]><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"><rect width="1" height="1"/></svg>')['error']);
        $this->assertStringContainsString('rozměry', (string) SvgQrSanitizer::sanitize('<svg xmlns="http://www.w3.org/2000/svg"><rect width="1" height="1"/></svg>')['error']);
        $this->assertStringContainsString('tvary', (string) SvgQrSanitizer::sanitize('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"><text>x</text></svg>')['error']);
        $this->assertStringContainsString('poškozený', (string) SvgQrSanitizer::sanitize('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"><rect>')['error']);
        $this->assertStringContainsString('1 MB', (string) SvgQrSanitizer::sanitize('<svg' . str_repeat(' ', SvgQrSanitizer::MAX_BYTES))['error']);
        $this->assertNull(SvgQrSanitizer::sanitize(self::QR)['error']);
    }

    public function testPublicUrl(): void {
        $base = 'https://www.veletrhprace.cz/';
        $this->assertTrue(SvgQrSanitizer::isPublicUrl('https://WWW.veletrhprace.cz/events/v1/r/skoda/qr', $base));
        $this->assertFalse(SvgQrSanitizer::isPublicUrl('http://www.veletrhprace.cz/events/v1/r/skoda/qr', $base));
        $this->assertFalse(SvgQrSanitizer::isPublicUrl('https://qrd.by/abc', $base));
        $this->assertFalse(SvgQrSanitizer::isPublicUrl(null, $base));
    }

    public function testDecodedUrl(): void {
        $this->assertSame('https://qrd.by/abc', PosterDataService::normalizeDecoded('  https://qrd.by/abc '));
        $this->assertNull(PosterDataService::normalizeDecoded('javascript:alert(1)'));
        $this->assertNull(PosterDataService::normalizeDecoded('text bez adresy'));
        $this->assertNull(PosterDataService::normalizeDecoded('https://x/' . str_repeat('a', 1000)));
        $this->assertNull(PosterDataService::normalizeDecoded(null));

        $base = 'https://www.veletrhprace.cz/';
        $this->assertSame('link', PosterDataService::decodedKind('https://www.veletrhprace.cz/events/v1/r/skoda/billboard', $base, 'skoda'));
        $this->assertSame('link', PosterDataService::decodedKind('https://www.veletrhprace.cz/events/v1/r/skoda', $base, 'skoda'));
        $this->assertSame('external', PosterDataService::decodedKind('https://www.veletrhprace.cz/events/v1/r/skodovka/fb', $base, 'skoda'));
        $this->assertSame('external', PosterDataService::decodedKind('https://qrd.by/abc', $base, 'skoda'));
        $this->assertSame('unknown', PosterDataService::decodedKind(null, $base, 'skoda'));
    }

    public function testCompanyAddress(): void {
        $this->assertSame('Škoda Auto a.s., tř. Václava Klementa 869, 29301 Mladá Boleslav',
            PosterDataService::address(['addr_name' => 'Škoda Auto a.s.', 'lokace' => 'tř. Václava Klementa 869', 'psc' => '29301', 'obec' => 'Mladá Boleslav']));
        $this->assertSame('Brno', PosterDataService::address(['addr_name' => '', 'lokace' => ' ', 'psc' => null, 'obec' => 'Brno']));
        $this->assertSame('', PosterDataService::address([]));
    }

    public function testPosterTextDefault(): void {
        $this->assertArrayHasKey('poster', PromoConfig::fromArray([])->texts);
        $this->assertStringContainsString('{url}', PromoConfig::fromArray([])->texts['poster']);
        $this->assertSame('vlastní {url}', PromoConfig::fromArray(['texts' => ['poster' => 'vlastní {url}']])->texts['poster']);
    }
}
