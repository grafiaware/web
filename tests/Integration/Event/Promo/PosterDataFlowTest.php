<?php
declare(strict_types=1);

namespace Test\Integration\Event\Promo;

use Container\EventsModelContainerConfigurator;
use Events\Model\Dao\Promo\PromoLinkDao;
use Events\Model\Dao\Promo\PromoLinkVisitDao;
use Events\Model\Dao\Promo\PromoQrSvgDao;
use Events\Service\Promo\PosterDataService;
use Events\Service\Promo\PromoConfig;
use Events\Service\Promo\PromoLinkService;
use Pes\Container\Container;
use Test\AppRunner\AppRunner;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

/**
 * F3 (#47): QR z QRdirectoru a podklad plakátu proti DB events – nahrání SVG (vyčištění, kanál, nepřečtený QR),
 * smazání, zástupce firmy, data podkladu plakátu s adresou a zveřejněnými sítěmi firmy.
 * Data testu mají prefix a po testu se smažou (vývojová data zůstanou).
 */
final class PosterDataFlowTest extends AppRunner {

    private const PREFIX = 'f3t-';
    private const COMPANY = 'f3-test-firma s.r.o.';
    private const LOGIN = 'f3_test_repre';
    private const SVG = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21"><script>x()</script><rect width="21" height="21" fill="#fff"/><path d="M0 0h7v7H0z"/></svg>';

    private static Container $c;
    private static int $companyId;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        self::$c = (new EventsModelContainerConfigurator())->configure((new TestDbEventsContainerConfigurator())->configure(new Container()));
        self::cleanup();
        $dao = self::$c->get(PromoLinkDao::class);
        $version = $dao->row("SELECT version FROM company_version ORDER BY version DESC LIMIT 1")['version'];
        $dao->exec("INSERT INTO company (name, version_fk) VALUES (:n, :v)", ['n' => self::COMPANY, 'v' => $version]);
        self::$companyId = $dao->lastId();
        $dao->exec("INSERT INTO company_address (company_id, name, lokace, psc, obec) VALUES (:id, 'F3 test', 'Hlavní 1', '60200', 'Brno')",
            ['id' => self::$companyId]);
        $fb = $dao->row("SELECT id FROM network WHERE channel = 'fb'");
        $ig = $dao->row("SELECT id FROM network WHERE channel = 'ig'");
        if ($fb !== null && $ig !== null) {
            $dao->exec("INSERT INTO company_to_network (company_id, network_id, link, published) VALUES (:c, :n, 'https://facebook.com/f3test', 1)",
                ['c' => self::$companyId, 'n' => $fb['id']]);
            $dao->exec("INSERT INTO company_to_network (company_id, network_id, link, published) VALUES (:c, :n, 'https://instagram.com/neverejne', 0)",
                ['c' => self::$companyId, 'n' => $ig['id']]);
        }
    }

    public static function tearDownAfterClass(): void {
        self::cleanup();
    }

    private static function cleanup(): void {
        $dao = self::$c->get(PromoLinkDao::class);
        $dao->exec("DELETE FROM promo_link WHERE code LIKE :p OR name LIKE :n", ['p' => self::PREFIX . '%', 'n' => 'f3-test-%']);   // kaskáda: SVG
        $dao->exec("DELETE FROM company WHERE name LIKE :n", ['n' => 'f3-test-%']);   // kaskáda: adresa, sítě, zástupci
    }

    private function links(): PromoLinkService {
        return new PromoLinkService(self::$c->get(PromoLinkDao::class), self::$c->get(PromoLinkVisitDao::class), PromoConfig::fromArray([]));
    }

    private function posters(): PosterDataService {
        return new PosterDataService(self::$c->get(PromoLinkDao::class), self::$c->get(PromoQrSvgDao::class), $this->links());
    }

    private function createLink(string $code, ?int $companyId = null): array {
        $r = $this->links()->save(null, ['code' => self::PREFIX . $code, 'name' => 'f3-test-' . $code,
            'owner_type' => $companyId ? 'exhibitor' : 'organizer', 'company_id' => $companyId, 'target' => 'home-register', 'active' => 1,
            'texts' => ['poster' => 'Přijďte! {url}']], 'f3-test');
        $this->assertSame([], $r['errors']);
        return $this->links()->link($r['id']);
    }

    public function testUploadListAndDeleteSvg(): void {
        $p = $this->posters();
        $link = $this->createLink('svg');
        $id = (int) $link['id'];

        $bad = $p->addSvg($id, 'neexistuje', '', 'není svg', null, 'f3-test');
        $this->assertCount(2, $bad['errors']);

        $ok = $p->addSvg($id, 'billboard', '', self::SVG, 'https://qrd.by/f3', 'f3-test');
        $this->assertSame([], $ok['errors']);
        $this->assertSame([], $ok['warnings']);
        $unread = $p->addSvg($id, 'letak', 'leták A5', self::SVG, 'nesmysl', 'f3-test');
        $this->assertCount(1, $unread['warnings'], 'nepřečtený QR = upozornění, uloží se');

        $list = $p->svgList($id);
        $this->assertCount(2, $list);
        $this->assertSame('Billboard', $list[0]['label'], 'prázdný popisek = název kanálu');
        $this->assertSame('https://qrd.by/f3', $list[0]['decoded_url']);
        $this->assertNull($list[1]['decoded_url']);
        $svgs = $p->svgs($id);
        $this->assertStringNotContainsString('script', $svgs[0]['svg']);
        $this->assertNotEmpty(array_filter($p->allSvgs(), fn($s) => $s['code'] === self::PREFIX . 'svg'));

        $this->assertFalse($p->deleteSvg($id + 100000, (int) $list[0]['id']), 'SVG jiného odkazu nesmaže');
        $this->assertTrue($p->deleteSvg($id, (int) $list[0]['id']));
        $this->assertCount(1, $p->svgList($id));
    }

    public function testRepresentative(): void {
        $p = $this->posters();
        $dao = self::$c->get(PromoLinkDao::class);
        $login = $dao->row("SELECT login_name FROM login WHERE login_name = :l", ['l' => self::LOGIN]);
        if ($login === null) {
            $dao->exec("INSERT INTO login (login_name) VALUES (:l)", ['l' => self::LOGIN]);
        }
        $dao->exec("INSERT INTO representative (company_id, login_login_name) VALUES (:c, :l)", ['c' => self::$companyId, 'l' => self::LOGIN]);
        $this->assertTrue($p->representsCompany(self::LOGIN, self::$companyId));
        $this->assertFalse($p->representsCompany(self::LOGIN, self::$companyId + 100000));
        $this->assertFalse($p->representsCompany('', self::$companyId));
        $this->assertFalse($p->representsCompany(self::LOGIN, null));
        $dao->exec("DELETE FROM representative WHERE login_login_name = :l", ['l' => self::LOGIN]);
        if ($login === null) {
            $dao->exec("DELETE FROM login WHERE login_name = :l", ['l' => self::LOGIN]);
        }
    }

    public function testPosterData(): void {
        $p = $this->posters();
        $link = $this->createLink('firma', self::$companyId);
        $p->addSvg((int) $link['id'], 'plakat', 'plakát A3', self::SVG, 'https://qrd.by/x', 'f3-test');

        $admin = $p->posterData($link, 'https://www.veletrhprace.cz/', true);
        $kinds = array_column($admin['objects'], 'kind');
        $this->assertSame('qr-web', $kinds[0]);
        $this->assertSame('https://www.veletrhprace.cz/events/v1/r/' . self::PREFIX . 'firma/plakat', $admin['objects'][0]['url']);
        $this->assertContains('qr-svg', $kinds);
        $byLabel = array_column(array_filter($admin['objects'], fn($o) => $o['kind'] === 'text'), 'text', 'label');
        $this->assertSame('Přijďte! www.veletrhprace.cz/events/v1/r/' . self::PREFIX . 'firma', $byLabel['Text plakátu']);
        $this->assertSame('www.veletrhprace.cz/events/v1/r/' . self::PREFIX . 'firma', $byLabel['Adresa k opsání']);
        $this->assertSame(self::COMPANY, $byLabel['Název firmy']);
        $this->assertSame('F3 test, Hlavní 1, 60200 Brno', $byLabel['Adresa firmy']);
        $texts = implode("\n", array_column($admin['objects'], 'text'));
        $this->assertStringNotContainsString('neverejne', $texts, 'nezveřejněná síť na plakát nepatří');

        $repre = $p->posterData($link, 'https://www.veletrhprace.cz/', false);
        $this->assertNotContains('qr-svg', array_column($repre['objects'], 'kind'), 'zástupce firmy dostane jen QR webu');
    }
}
