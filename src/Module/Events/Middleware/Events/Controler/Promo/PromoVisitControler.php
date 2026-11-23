<?php

namespace Events\Middleware\Events\Controler\Promo;

use Auth\Service\Source\VisitSource;
use Auth\Service\Source\VisitSourceStore;
use Events\Service\Promo\PromoLinkService;
use Pes\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * F1 + F2 (#47, docs/vp/zadani/F1.md): návštěva trvalého odkazu a zdroj návštěvy.
 *
 * GET /events/v1/r/<kód>[/<kanál>]: započítá návštěvu (součet za den a kanál, ne roboti), zapamatuje zdroj
 * (session, se souhlasem „analytika“ i cookie vp_zdroj) a přesměruje 302 na aktuální cíl. Vypnutý nebo neznámý
 * odkaz vede na úvodní stránku a nic nezapočítá. Přepínač vp.odkazy vypnutý: routa 404 – adresy s /events/v1/r/
 * vznikají až s funkcí.
 *
 * GET /events/v1/source[?utm_…]: skript webu (vp-zdroj.js) – uloží UTM z adresy stránky a srovná cookie se souhlasem.
 */
class PromoVisitControler extends PromoControlerAbstract {

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            PromoLinkService $links,
            private VisitSourceStore $sources) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $links);
    }

    public function visit(ServerRequestInterface $request, $code, $channel = null): ResponseInterface {
        $root = $this->rootPath($request);
        $isBot = PromoLinkService::isBot($request->getHeaderLine('User-Agent'));
        try {
            $result = $this->links->visit((string) $code, $channel === null ? null : (string) $channel, $isBot);
        } catch (\Throwable $e) {
            error_log('F2 promo link visit failed: ' . get_class($e) . ': ' . $e->getMessage());
            $result = ['target' => '', 'counted' => false, 'source' => null];
        }
        $response = (new Response())->withStatus(302)
            ->withHeader('Location', $root . $result['target'])
            ->withHeader('Cache-Control', 'no-store')
            ->withHeader('Referrer-Policy', 'no-referrer-when-downgrade')
            ->withHeader('X-Robots-Tag', 'noindex');
        if ($result['source'] instanceof VisitSource) {
            try {
                $response = $this->sources->remember($request, $response, $result['source'], $root);
            } catch (\Throwable $e) {
                error_log('F1 visit source failed: ' . $e->getMessage());
            }
        }
        return $response;
    }

    public function source(ServerRequestInterface $request): ResponseInterface {
        $response = $this->createJsonOKResponse(['ok' => true])->withHeader('Cache-Control', 'no-store');
        try {
            $utm = VisitSource::fromUtm($request->getQueryParams(), $this->links->config()->now());
            if ($utm !== null && !PromoLinkService::isBot($request->getHeaderLine('User-Agent'))) {
                return $this->sources->remember($request, $response, $utm, $this->rootPath($request));
            }
            return $this->sources->syncCookie($request, $response, $this->rootPath($request));
        } catch (\Throwable $e) {
            error_log('F1 visit source sync failed: ' . $e->getMessage());
            return $response;
        }
    }
}
