<?php
declare(strict_types=1);

namespace Test\Unit\Events\Promo;

use Auth\Service\Source\VisitSource;
use Auth\Service\Source\VisitSourceStore;
use DateTimeImmutable;
use Events\Service\Promo\PromoConfig;
use Events\Service\Promo\PromoLinkService;
use PHPUnit\Framework\TestCase;

/**
 * F1 + F2 (#47): pravidla odkazů – kód a kód z názvu, kanál, roboti, cíl na stránku webu (open redirect),
 * zdroj návštěvy (UTM, cookie), souhlas z cookie lišty, souhrn přehledu, konfigurace.
 */
final class PromoRulesTest extends TestCase {

    public function testCodeValidation(): void {
        foreach (['skoda', 'zs-komenskeho', 'up-brno', 'a1', 'vp-billboard-2027'] as $ok) {
            $this->assertTrue(PromoLinkService::isValidCode($ok), $ok);
        }
        foreach (['', 'a', '1abc', 'Škoda', 'sko da', 'skoda_', str_repeat('a', 31), '-ab'] as $bad) {
            $this->assertFalse(PromoLinkService::isValidCode($bad), $bad);
        }
        $this->assertSame('skoda', PromoLinkService::normalizeCode('  Skoda '));
    }

    public function testSlugFromCompanyName(): void {
        $this->assertSame('berger-bohemia', PromoLinkService::slug('BERGER BOHEMIA a. s. '));
        $this->assertSame('skoda-auto', PromoLinkService::slug('ŠKODA AUTO a.s.'));
        $this->assertSame('ceska-posta', PromoLinkService::slug('Česká pošta, s. p.'));
        $this->assertSame('zs-komenskeho-plzen', PromoLinkService::slug('ZŠ Komenského, Plzeň'));
        $this->assertSame('f-4flow-management-gmbh-organi', PromoLinkService::slug('4flow management GmbH organizační složka'));
        $this->assertSame('odkaz', PromoLinkService::slug('!!!'));
        foreach (['Žluťoučký kůň s.r.o.', 'Agentur für Arbeit Cham', 'x', '12345'] as $name) {
            $this->assertTrue(PromoLinkService::isValidCode(PromoLinkService::slug($name)), $name);
        }
    }

    public function testCodeFromNameCollision(): void {
        $taken = ['skoda', 'skoda-2'];
        $this->assertSame('skoda-3', PromoLinkService::codeFromName('Škoda', fn($c) => in_array($c, $taken, true)));
        $long = str_repeat('abcde', 6);   // 30 znaků
        $code = PromoLinkService::codeFromName($long, fn($c) => $c === $long);
        $this->assertSame(30, strlen($code));
        $this->assertStringEndsWith('-2', $code);
    }

    public function testChannelFor(): void {
        $known = ['fb' => [], 'plakat' => []];
        $this->assertSame('fb', PromoLinkService::channelFor('FB', $known));
        $this->assertSame(PromoLinkService::CHANNEL_NONE, PromoLinkService::channelFor(null, $known));
        $this->assertSame(PromoLinkService::CHANNEL_NONE, PromoLinkService::channelFor('', $known));
        $this->assertSame(PromoLinkService::CHANNEL_OTHER, PromoLinkService::channelFor('neco', $known));
    }

    public function testBots(): void {
        foreach (['facebookexternalhit/1.1', 'LinkedInBot/1.0', 'Twitterbot/1.0', 'WhatsApp/2.23', 'Googlebot/2.1', 'curl/8.0', ''] as $ua) {
            $this->assertTrue(PromoLinkService::isBot($ua), $ua);
        }
        $this->assertFalse(PromoLinkService::isBot('Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148'));
    }

    public function testTargetPathOnlyOwnSite(): void {
        $hosts = ['najdisi.cz', 'www.najdisi.cz'];
        $n = fn($v, $base = '/') => PromoLinkService::normalizeTargetPath($v, $hosts, $base);
        $this->assertSame('web/v1/page/item/abc?x=1#k', $n('https://najdisi.cz/web/v1/page/item/abc?x=1#k'));
        $this->assertSame('web/v1/page/item/abc', $n('/web/v1/page/item/abc'));
        $this->assertSame('web/v1/page/item/abc', $n('web/v1/page/item/abc'));
        $this->assertSame('', $n('https://WWW.najdisi.cz/'));
        $this->assertSame('web/v1/page/item/abc', $n('http://najdisi.cz/web/web/v1/page/item/abc', '/web/'));
        $this->assertSame('', $n('/web', '/web/'));
        $this->assertNull($n('https://evil.example/x'));
        $this->assertNull($n('//evil.example/x'));
        $this->assertNull($n('javascript:alert(1)'));
        $this->assertNull($n('https://user:pw@najdisi.cz/'));
        $this->assertNull($n('/jinde/x', '/web/'));
        $this->assertNull($n('events/v1/r/skoda'));          // smyčka na odkaz
        $this->assertNull($n("web/v1\npage"));
        $this->assertNull($n('a b'));
        $this->assertNull($n(''));
    }

    public function testTargetUri(): void {
        $s = new PromoLinkService($this->createStub(\Events\Model\Dao\Promo\PromoLinkDao::class),
            $this->createStub(\Events\Model\Dao\Promo\PromoLinkVisitDao::class), new PromoConfig());
        $this->assertSame('?modal=register0AXR2026', $s->targetUri(['active' => 1, 'target' => 'home-register', 'target_path' => null]));
        $this->assertSame('', $s->targetUri(['active' => 1, 'target' => 'home', 'target_path' => null]));
        $this->assertSame('web/x', $s->targetUri(['active' => 1, 'target' => 'page', 'target_path' => 'web/x']));
        $this->assertSame('', $s->targetUri(['active' => 0, 'target' => 'page', 'target_path' => 'web/x']));
        $this->assertSame('', $s->targetUri(['active' => 1, 'target' => 'neexistuje', 'target_path' => null]));
    }

    public function testVisitSourceRoundTrip(): void {
        $now = new DateTimeImmutable('2026-11-24 10:00:00');
        $s = VisitSource::fromLink('skoda', 'fb', $now);
        $back = VisitSource::decode($s->encode());
        $this->assertSame('skoda', $back->linkCode);
        $this->assertSame('fb', $back->channel);
        $this->assertSame('2026-11-24 10:00:00', $back->landingAt);
        $this->assertNull(VisitSource::fromLink('Neplatný kód', null, $now));
        $this->assertNull(VisitSource::decode('nesmysl'));
        $this->assertNull(VisitSource::decode(rtrim(strtr(base64_encode('{"c":"<script>","t":"2026-11-24 10:00:00"}'), '+/', '-_'), '=')));
    }

    public function testUtm(): void {
        $now = new DateTimeImmutable('2026-11-24 10:00:00');
        $u = VisitSource::fromUtm(['utm_source' => ' facebook ', 'utm_medium' => 'cpc', 'utm_campaign' => str_repeat('x', 150)], $now);
        $this->assertSame('facebook', $u->utmSource);
        $this->assertSame(100, mb_strlen($u->utmCampaign));
        $this->assertNull($u->linkCode);
        $this->assertNull(VisitSource::fromUtm(['utm_medium' => 'cpc'], $now));
        $this->assertNull(VisitSource::fromUtm(['utm_source' => ['pole']], $now));
        $this->assertSame('ab', VisitSource::utm("a<\x01b>"));
    }

    public function testAnalyticsConsent(): void {
        $req = function (array $cookies) {
            $r = $this->createStub(\Psr\Http\Message\ServerRequestInterface::class);
            $r->method('getCookieParams')->willReturn($cookies);
            return $r;
        };
        $this->assertFalse(VisitSourceStore::hasAnalyticsConsent($req([])));
        $this->assertFalse(VisitSourceStore::hasAnalyticsConsent($req(['cc_cookie' => '{"categories":["necessary"]}'])));
        $this->assertTrue(VisitSourceStore::hasAnalyticsConsent($req(['cc_cookie' => '{"categories":["necessary","analytics"]}'])));
        $this->assertTrue(VisitSourceStore::hasAnalyticsConsent($req(['cc_cookie' => rawurlencode('{"categories":["analytics"]}')])));
        $this->assertFalse(VisitSourceStore::hasAnalyticsConsent($req(['cc_cookie' => 'nesmysl'])));
    }

    public function testOverview(): void {
        $links = [
            ['id' => 1, 'code' => 'skoda', 'owner_type' => 'exhibitor', 'name' => 'Škoda'],
            ['id' => 2, 'code' => 'up-brno', 'owner_type' => 'labour_office', 'name' => 'ÚP Brno'],
        ];
        $visits = [['link_id_fk' => 1, 'channel' => 'fb', 'visits' => 10], ['link_id_fk' => 1, 'channel' => 'plakat', 'visits' => 5],
            ['link_id_fk' => 2, 'channel' => '-', 'visits' => 3]];
        $regs = [
            ['link_code' => 'skoda', 'channel' => 'fb', 'utm_source' => null, 'utm_campaign' => null, 'registered' => 4, 'confirmed' => 3],
            ['link_code' => 'skoda', 'channel' => null, 'utm_source' => null, 'utm_campaign' => null, 'registered' => 1, 'confirmed' => 0],
            ['link_code' => null, 'channel' => null, 'utm_source' => 'facebook', 'utm_campaign' => 'vpv2027', 'registered' => 2, 'confirmed' => 1],
            ['link_code' => 'smazany', 'channel' => null, 'utm_source' => null, 'utm_campaign' => null, 'registered' => 9, 'confirmed' => 9],
        ];
        $o = PromoLinkService::overview($links, $visits, $regs);
        $this->assertSame(15, $o['links'][0]['visits']);
        $this->assertSame(5, $o['links'][0]['registered']);
        $this->assertSame(4, $o['links'][0]['channels']['fb']['registered']);
        $this->assertSame(1, $o['links'][0]['channels']['-']['registered']);
        $this->assertSame('facebook / vpv2027', $o['utm'][0]['label']);
        $this->assertSame(['visits' => 18, 'registered' => 7, 'confirmed' => 4], $o['totals']);

        $f = PromoLinkService::overview($links, $visits, $regs, 'labour_office');
        $this->assertCount(1, $f['links']);
        $this->assertSame([], $f['utm']);
        $this->assertSame(3, $f['totals']['visits']);
    }

    public function testConfig(): void {
        $c = PromoConfig::fromArray([]);
        $this->assertSame(30, $c->sourceCookieDays);
        $this->assertSame('home-register', $c->companyLinkTarget);
        $this->assertArrayHasKey(PromoConfig::TARGET_PAGE, $c->targets);
        $c2 = PromoConfig::fromArray(['targets' => ['home' => ['label' => 'Úvod', 'path' => '']], 'companyLinkTarget' => 'neni',
            'sourceCookieDays' => 0, 'texts' => ['social' => 'Ahoj {url}']]);
        $this->assertSame('home', $c2->companyLinkTarget);
        $this->assertArrayHasKey(PromoConfig::TARGET_PAGE, $c2->targets);
        $this->assertSame(1, $c2->sourceCookieDays);
        $this->assertSame('Ahoj {url}', $c2->texts['social']);
        $this->assertArrayHasKey('mail', $c2->texts);
    }
}
