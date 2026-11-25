<?php
declare(strict_types=1);

namespace Test\Integration\Event\Promo;

use Container\EventsModelContainerConfigurator;
use Events\Model\Dao\Promo\PromoLinkDao;
use Events\Model\Dao\Promo\PromoLinkVisitDao;
use Events\Service\Promo\PromoConfig;
use Events\Service\Promo\PromoLinkService;
use Pes\Container\Container;
use Test\AppRunner\AppRunner;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

/**
 * F2 (#47): trvalé odkazy proti DB events – založení, kontrola kódu, návštěvy po dnech a kanálech (ne roboti),
 * vypnutý odkaz, změna cíle a historie, zámek kódu po první návštěvě, odkaz firmy pro promo kit, kanály z tabulky network.
 * Data testu mají prefix a po testu se smažou (vývojová data zůstanou).
 */
final class PromoLinkFlowTest extends AppRunner {

    private const PREFIX = 'f1t-';
    private const COMPANY = 'f1-test-firma Žluťoučká s.r.o.';

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
    }

    public static function tearDownAfterClass(): void {
        self::cleanup();
    }

    private static function cleanup(): void {
        $dao = self::$c->get(PromoLinkDao::class);
        $dao->exec("DELETE FROM promo_link WHERE code LIKE :p OR name LIKE :n", ['p' => self::PREFIX . '%', 'n' => 'f1-test-%']);   // kaskáda: návštěvy, historie
        $dao->exec("DELETE FROM company WHERE name LIKE :n", ['n' => 'f1-test-%']);
    }

    private function service(): PromoLinkService {
        return new PromoLinkService(self::$c->get(PromoLinkDao::class), self::$c->get(PromoLinkVisitDao::class), PromoConfig::fromArray([]));
    }

    private function create(string $code, array $override = []): int {
        $r = $this->service()->save(null, $override + ['code' => self::PREFIX . $code, 'name' => 'f1-test-' . $code, 'owner_type' => 'organizer',
            'target' => 'home-register', 'active' => 1], 'f1-test');
        $this->assertSame([], $r['errors']);
        return $r['id'];
    }

    public function testCreateValidateAndDuplicate(): void {
        $s = $this->service();
        $this->create('a');
        $dup = $s->save(null, ['code' => self::PREFIX . 'a', 'name' => 'x', 'owner_type' => 'organizer', 'target' => 'home'], 'f1-test');
        $this->assertNotEmpty($dup['errors']);
        $bad = $s->save(null, ['code' => 'Špatný', 'name' => '', 'owner_type' => 'nikdo', 'target' => 'page', 'target_path' => null], 'f1-test');
        $this->assertCount(4, $bad['errors']);
    }

    public function testVisitsCountPerDayAndChannel(): void {
        $s = $this->service();
        $id = $this->create('b');
        $r = $s->visit(self::PREFIX . 'b', 'fb', false);
        $this->assertSame('?modal=register0AXR2026', $r['target']);
        $this->assertSame('fb', $r['source']->channel);
        $s->visit(strtoupper(self::PREFIX . 'b'), 'FB', false);
        $s->visit(self::PREFIX . 'b', null, false);
        $s->visit(self::PREFIX . 'b', 'nevim', false);
        $bot = $s->visit(self::PREFIX . 'b', 'fb', true);
        $this->assertFalse($bot['counted']);
        $this->assertNull($bot['source']);
        $byChannel = [];
        foreach ($s->visitsByChannel(null, null, $id) as $v) {
            $byChannel[$v['channel']] = (int) $v['visits'];
        }
        $this->assertSame(['-' => 1, 'fb' => 2, 'jiny' => 1], (ksort($byChannel) ? $byChannel : []));
        $this->assertSame(4, $s->visitCount($id));
        $this->assertCount(1, $s->daily($id, null, null));
    }

    public function testInactiveAndUnknownGoHome(): void {
        $s = $this->service();
        $id = $this->create('c');
        $s->save($id, ['name' => 'f1-test-c', 'owner_type' => 'organizer', 'target' => 'home-register', 'active' => 0], 'f1-test');
        $r = $s->visit(self::PREFIX . 'c', 'fb', false);
        $this->assertSame('', $r['target']);
        $this->assertFalse($r['counted']);
        $this->assertSame(0, $s->visitCount($id));
        $this->assertSame('', $s->visit(self::PREFIX . 'neni', 'fb', false)['target']);
        $this->assertSame('', $s->visit('../../x', null, false)['target']);
    }

    public function testTargetChangeHistoryAndCodeLock(): void {
        $s = $this->service();
        $id = $this->create('d');
        $ok = $s->save($id, ['code' => self::PREFIX . 'd2', 'name' => 'f1-test-d', 'owner_type' => 'organizer', 'target' => 'home-register', 'active' => 1], 'f1-test');
        $this->assertSame([], $ok['errors'], 'bez návštěv jde kód změnit');
        $s->visit(self::PREFIX . 'd2', 'plakat', false);
        $r = $s->save($id, ['code' => self::PREFIX . 'd3', 'name' => 'f1-test-d', 'owner_type' => 'organizer', 'target' => 'page',
            'target_path' => 'web/v1/page/item/xyz', 'active' => 1], 'f1-test');
        $this->assertNotEmpty($r['errors']);
        $r = $s->save($id, ['name' => 'f1-test-d', 'owner_type' => 'organizer', 'target' => 'page', 'target_path' => 'web/v1/page/item/xyz', 'active' => 1], 'f1-test');
        $this->assertSame([], $r['errors']);
        $this->assertSame('web/v1/page/item/xyz', $s->visit(self::PREFIX . 'd2', 'plakat', false)['target']);
        $history = $s->history($id);
        $this->assertCount(2, $history);
        $this->assertSame('page', $history[0]['target']);
    }

    public function testCompanyLink(): void {
        $s = $this->service();
        $link = $s->companyLink(self::$companyId, self::COMPANY, 'f1-test');
        $this->assertSame('f1-test-firma-zlutoucka', $link['code']);
        $this->assertSame('exhibitor', $link['owner_type']);
        $this->assertSame('home-register', $link['target']);
        $this->assertSame((int) $link['id'], (int) $s->companyLink(self::$companyId, self::COMPANY, 'f1-test')['id'], 'druhé zobrazení nový odkaz nezaloží');
    }

    public function testChannelsFromNetworkTable(): void {
        $channels = $this->service()->channels();
        foreach (['fb', 'ig', 'li', 'yt', 'x', 'tt', 'web'] as $code) {
            $this->assertArrayHasKey($code, $channels, $code);
        }
        $this->assertSame('social', $channels['tt']['group']);
        $this->assertSame('web', $channels['web']['group']);
        $this->assertSame('print', $channels[array_key_first($channels)]['group']);
        $this->assertSame('qr', $channels[array_key_last($channels)]['group']);
        $rows = $this->service()->copyRows(['code' => 'kod', 'name' => 'Firma', 'texts' => null], 'https://web.test/');
        $fb = array_values(array_filter($rows, fn($r) => $r['channel'] === 'fb'))[0];
        $this->assertSame('https://web.test/events/v1/r/kod/fb', $fb['url']);
        $this->assertStringContainsString('https://web.test/events/v1/r/kod/fb', $fb['text']);
    }
}
