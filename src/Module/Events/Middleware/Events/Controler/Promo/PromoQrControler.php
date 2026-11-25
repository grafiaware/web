<?php

namespace Events\Middleware\Events\Controler\Promo;

use Application\Feature\FeatureFlags;
use Auth\Service\Qr\QrImageGenerator;
use FrontControler\StatusEnum;
use Events\Service\Lottery\LotteryPaperService;
use Events\Service\Promo\PosterDataService;
use Events\Service\Promo\PromoLinkService;
use Events\Service\Promo\SvgQrSanitizer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * F3 (#47, docs/vp/zadani/F1.md): QR kódy a podklad plakátu.
 *
 * POST /events/v1/promo/:id/qr-svg             media manager: nahrání SVG z QRdirectoru (text SVG a adresa přečtená v prohlížeči)
 * POST /events/v1/promo/:id/qr-svg/:sid/delete media manager: smazání SVG
 * GET  /events/v1/promo/:id/qr.png             media manager, zástupce firmy odkazu: QR webu (kanál qr) jako PNG
 * GET  /events/v1/promo/:id/poster             media manager, zástupce firmy odkazu: data podkladu plakátu (JSON) pro PDF v prohlížeči
 * GET  /events/v1/qr-check                     media manager: ověření všech druhů QR, které web vydává nebo má nahrané
 *
 * Routy F3 jsou za přepínačem vp.qr-plakaty a navíc vyžadují vp.odkazy; ověření QR za vp.odkazy nebo vp.losovani.
 */
class PromoQrControler extends PromoControlerAbstract {

    /** Pevný QR registrace z tištěných materiálů 2026 (Auth QrImageControler) – zůstává v provozu, ověřuje se. */
    private const LEGACY_REGISTER_QR = 'https://php8.najdisi.cz/?modal=register0AXR2026';

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            PromoLinkService $links,
            private PosterDataService $posters,
            private QrImageGenerator $qr) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $links);
    }

    private function enabled(string $flag): bool {
        return FeatureFlags::fromConfiguration()->isEnabled($flag);
    }

    private function notFound(ServerRequestInterface $request): ResponseInterface {
        return $this->textPage($request, 'Nenalezeno', 'Stránka neexistuje.', 404);
    }

    /** Odkaz, ke kterému má přihlášený přístup (media manager všechny, zástupce firmy jen odkaz své firmy), jinak null. */
    private function accessibleLink($id): ?array {
        $link = $this->links->link((int) $id);
        if ($link === null) {
            return null;
        }
        if ($this->isAdmin()) {
            return $link;
        }
        return $link['owner_type'] === 'exhibitor' && $this->posters->representsCompany($this->loginName(), (int) $link['company_id_fk'])
            ? $link : null;
    }

    // ---------------------------------------------------------------- SVG z QRdirectoru

    public function uploadSvg(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->enabled('vp.odkazy')) {
            return $this->notFound($request);
        }
        if (!$this->isAdmin()) {
            $this->addFlashMessage('QR kódy může nahrávat jen media manager.', FlashSeverityEnum::WARNING);
            return $this->createResponseRedirectSeeOther($request, 'events/v1/promo');
        }
        if ($this->links->link((int) $id) === null) {
            $this->addFlashMessage('Odkaz neexistuje.', FlashSeverityEnum::WARNING);
            return $this->createResponseRedirectSeeOther($request, 'events/v1/promo');
        }
        $svg = $this->param($request, 'svg_text', '');
        $result = $this->posters->addSvg((int) $id, (string) $this->param($request, 'channel', ''), (string) $this->param($request, 'label', ''),
            is_string($svg) ? $svg : '', is_string($u = $this->param($request, 'decoded_url')) ? $u : null, $this->loginName());
        foreach ($result['errors'] as $e) {
            $this->addFlashMessage($e, FlashSeverityEnum::WARNING);
        }
        if (!$result['errors']) {
            $this->addFlashMessage('QR kód nahrán.', FlashSeverityEnum::SUCCESS);
            foreach ($result['warnings'] as $w) {
                $this->addFlashMessage($w, FlashSeverityEnum::WARNING);
            }
        }
        return $this->createResponseRedirectSeeOther($request, "events/v1/promo/{$id}#qr");
    }

    public function deleteSvg(ServerRequestInterface $request, $id, $sid): ResponseInterface {
        if (!$this->enabled('vp.odkazy')) {
            return $this->notFound($request);
        }
        if (!$this->isAdmin()) {
            $this->addFlashMessage('QR kódy může mazat jen media manager.', FlashSeverityEnum::WARNING);
            return $this->createResponseRedirectSeeOther($request, 'events/v1/promo');
        }
        $this->addFlashMessage($this->posters->deleteSvg((int) $id, (int) $sid) ? 'QR kód smazán.' : 'QR kód nenalezen.',
            FlashSeverityEnum::SUCCESS);
        return $this->createResponseRedirectSeeOther($request, "events/v1/promo/{$id}#qr");
    }

    // ---------------------------------------------------------------- QR webu a podklad plakátu

    public function qrPng(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->enabled('vp.odkazy')) {
            return $this->notFound($request);
        }
        $link = $this->accessibleLink($id);
        if ($link === null) {
            return $this->textPage($request, 'Přístup odepřen', 'QR kód odkazu stáhne jen media manager nebo zástupce firmy odkazu.', 403);
        }
        $url = $this->absoluteUrl($request, PromoLinkService::ROUTE . $link['code'] . '/qr');
        $png = $this->qr->generate($url, preg_replace('#^https?://#', '', $url))->getString();   // popisek = adresa k opsání
        return $this->createStringOKResponse($png)
            ->withHeader('Content-Type', 'image/png')
            ->withHeader('Content-Disposition', 'attachment; filename="qr-' . $link['code'] . '.png"')
            ->withHeader('Cache-Control', 'no-store');
    }

    public function poster(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->enabled('vp.odkazy')) {
            return $this->notFound($request);
        }
        $link = $this->accessibleLink($id);
        if ($link === null) {
            return $this->createJsonOKResponse(['error' => 'Podklad plakátu stáhne jen media manager nebo zástupce firmy odkazu.'],
                StatusEnum::_403_Forbidden)->withHeader('Cache-Control', 'no-store');
        }
        $data = $this->posters->posterData($link, $this->absoluteUrl($request, ''), $this->isAdmin());
        return $this->createJsonOKResponse($data)->withHeader('Cache-Control', 'no-store');
    }

    // ---------------------------------------------------------------- ověření QR

    public function qrCheck(ServerRequestInterface $request): ResponseInterface {
        if (!$this->enabled('vp.odkazy') && !$this->enabled('vp.losovani')) {
            return $this->notFound($request);
        }
        if (!$this->isAdmin()) {
            return $this->textPage($request, 'Přístup odepřen', 'Ověření QR kódů je jen pro media managera. Přihlaste se účtem s rolí media_manager.', 403);
        }
        $base = $this->absoluteUrl($request, '');
        $items = [];
        if ($this->enabled('vp.losovani')) {
            // vzorové kódy – jen tvar adresy, nic se nezapíše a nikomu nepatří
            $items[] = ['group' => 'Losování (D1)', 'label' => 'Kód návštěvníka (vzor)', 'url' => $base . 'events/v1/scan/ABCDEFGHJK23', 'source' => 'web'];
            $items[] = ['group' => 'Losování (D1c)', 'label' => 'Ústřižek papírového lístku (vzor)',
                        'url' => $base . 'events/v1/scan/' . LotteryPaperService::scanCode('ABCDEFGHJK'), 'source' => 'web'];
        }
        if ($this->enabled('vp.odkazy')) {
            foreach ($this->links->links() as $l) {
                if ((int) $l['active']) {
                    $items[] = ['group' => 'Odkazy (F2)', 'label' => $l['name'] . ' – QR webu', 'url' => $base . PromoLinkService::ROUTE . $l['code'] . '/qr',
                                'source' => 'web', 'linkId' => (int) $l['id']];
                }
            }
            if ($this->enabled('vp.qr-plakaty')) {
                foreach ($this->posters->allSvgs() as $s) {
                    $items[] = ['group' => 'QRdirector (F3)', 'label' => $s['link_name'] . ' – ' . $s['label'], 'url' => $s['decoded_url'],
                                'source' => 'svg', 'linkId' => (int) $s['link_id_fk'],
                                'kind' => PosterDataService::decodedKind($s['decoded_url'], $base, (string) $s['code'])];
                }
            }
        }
        $items[] = ['group' => 'Tištěné materiály 2026', 'label' => 'Starý QR registrace (/auth/v1/qrimage/register)', 'url' => self::LEGACY_REGISTER_QR,
                    'source' => 'legacy'];
        foreach ($items as &$it) {
            $it['public'] = SvgQrSanitizer::isPublicUrl($it['url'], $base);
        }
        unset($it);
        return $this->page($request, 'Ověření QR kódů', 'qr-check.php', ['items' => $items, 'publicBase' => $base]);
    }
}
