<?php

namespace Events\Middleware\Events\Controler\Promo;

use Events\Service\Promo\PromoAuthLookup;
use Events\Service\Promo\PromoConfig;
use Events\Service\Promo\PromoLinkService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * F2 (#47, docs/vp/zadani/F1.md): správa trvalých odkazů a přehled zdrojů (jen media_manager) – seznam s návštěvami
 * a registracemi, založení a úprava odkazu (cíl, texty ke kopírování), historie cíle, návštěvy po dnech, export CSV.
 */
class PromoAdminControler extends PromoControlerAbstract {

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            PromoLinkService $links,
            private PromoAuthLookup $auth) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $links);
    }

    private function denied(ServerRequestInterface $request): ResponseInterface {
        return $this->textPage($request, 'Přístup odepřen', 'Tato stránka je jen pro media managera. Přihlaste se účtem s rolí media_manager.', 403);
    }

    /** POST (formulář) smí odpovědět jen přesměrováním – hláška a 303. */
    private function deniedPost(ServerRequestInterface $request): ResponseInterface {
        $this->addFlashMessage('Odkazy může spravovat jen media manager.', FlashSeverityEnum::WARNING);
        return $this->createResponseRedirectSeeOther($request, 'events/v1/promo');
    }

    /** Filtr období (dny 'Y-m-d') a vlastníka z dotazu. */
    private function filter(ServerRequestInterface $request): array {
        $day = fn($v) => is_string($v) && preg_match('/^\d{4}-\d\d-\d\d$/', $v) && strtotime($v) !== false ? $v : null;
        $owner = (string) $this->param($request, 'owner', '');
        return [
            'from' => $day($this->param($request, 'from')),
            'to' => $day($this->param($request, 'to')),
            'owner' => isset(PromoLinkService::OWNER_TYPES[$owner]) ? $owner : '',
        ];
    }

    private function overview(array $f, ?int $linkId = null): array {
        $links = $this->links->links();
        if ($linkId !== null) {
            $links = array_values(array_filter($links, fn($l) => (int) $l['id'] === $linkId));
        }
        try {
            $registrations = $this->auth->registrationCounts(
                $f['from'] !== null ? $f['from'] . ' 00:00:00' : null,
                $f['to'] !== null ? (new \DateTimeImmutable($f['to']))->modify('+1 day')->format('Y-m-d 00:00:00') : null);
            $authError = false;
        } catch (\Throwable $e) {
            error_log('F1 registration sources failed: ' . $e->getMessage());
            $registrations = [];
            $authError = true;
        }
        $visits = $this->links->visitsByChannel($f['from'], $f['to'], $linkId);
        return PromoLinkService::overview($links, $visits, $registrations, $f['owner']) + ['authError' => $authError];
    }

    public function index(ServerRequestInterface $request): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->denied($request);
        }
        $f = $this->filter($request);
        return $this->page($request, 'Odkazy a zdroje registrací', 'admin-list.php', [
            'filter' => $f,
            'overview' => $this->overview($f),
            'links' => $this->links,
            'companies' => $this->links->companies(),
            'config' => $this->links->config(),
        ]);
    }

    public function detail(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->denied($request);
        }
        $link = $this->links->link((int) $id);
        if ($link === null) {
            return $this->textPage($request, 'Odkazy', 'Odkaz neexistuje.', 404);
        }
        $f = $this->filter($request);
        $f['owner'] = '';
        $overview = $this->overview($f, (int) $id);
        return $this->page($request, $link['name'], 'admin-link.php', [
            'link' => $link,
            'filter' => $f,
            'row' => $overview['links'][0] ?? ['channels' => [], 'visits' => 0, 'registered' => 0, 'confirmed' => 0],
            'authError' => $overview['authError'],
            'daily' => $this->links->daily((int) $id, $f['from'], $f['to']),
            'history' => $this->links->history((int) $id),
            'visitCount' => $this->links->visitCount((int) $id),
            'copyRows' => $this->links->copyRows($link, $this->absoluteUrl($request, '')),
            'targetUrl' => $this->absoluteUrl($request, $this->links->targetUri($link)),
            'links' => $this->links,
            'companies' => $this->links->companies(),
            'config' => $this->links->config(),
        ]);
    }

    public function save(ServerRequestInterface $request, $id = null): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->deniedPost($request);
        }
        if ($id !== null && $this->links->link((int) $id) === null) {
            $this->addFlashMessage('Odkaz neexistuje.', FlashSeverityEnum::WARNING);
            return $this->createResponseRedirectSeeOther($request, 'events/v1/promo');
        }
        $data = [];
        foreach (['code', 'name', 'owner_type', 'company_id', 'target', 'note', 'active'] as $k) {
            $v = $this->param($request, $k);
            $data[$k] = is_array($v) ? '' : $v;
        }
        if ($id !== null && $this->param($request, 'code') === null) {
            unset($data['code']);   // formulář kód nepošle, když už odkaz má návštěvy
        }
        if ($id === null) {
            $data['active'] = 1;
        }
        $texts = $this->param($request, 'texts', []);
        $data['texts'] = is_array($texts) ? $texts : [];
        if (($data['target'] ?? '') === PromoConfig::TARGET_PAGE) {
            $data['target_path'] = PromoLinkService::normalizeTargetPath((string) $this->param($request, 'target_path', ''),
                $this->allowedHosts($request), $this->rootPath($request));
        }
        $result = $this->links->save($id === null ? null : (int) $id, $data, $this->loginName());
        foreach ($result['errors'] as $error) {
            $this->addFlashMessage($error, FlashSeverityEnum::WARNING);
        }
        if ($result['errors']) {
            return $this->createResponseRedirectSeeOther($request, $id === null ? 'events/v1/promo#novy' : "events/v1/promo/{$id}");
        }
        $this->addFlashMessage($id === null ? 'Odkaz založen.' : 'Odkaz uložen.', FlashSeverityEnum::SUCCESS);
        return $this->createResponseRedirectSeeOther($request, "events/v1/promo/{$result['id']}");
    }

    /** Export přehledu (CSV, UTF-8 s BOM, středník). */
    public function csv(ServerRequestInterface $request): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->denied($request);
        }
        $f = $this->filter($request);
        $out = fopen('php://temp', 'w+');
        fwrite($out, "\xEF\xBB\xBF");
        foreach ($this->links->csvRows($this->overview($f), $f['from'], $f['to']) as $row) {
            fputcsv($out, $row, ';', '"', '\\');
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return $this->createStringOKResponse($csv)
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="odkazy-zdroje-' . date('Y-m-d') . '.csv"')
            ->withHeader('Cache-Control', 'no-store');
    }

    /** Hostitelé, jejichž adresy smí být cílem odkazu: aktuální a veřejný (WEB_PUBLIC_HOST). */
    private function allowedHosts(ServerRequestInterface $request): array {
        $hosts = [strtolower($request->getUri()->getHost())];
        $public = parse_url($this->absoluteUrl($request, ''), PHP_URL_HOST);
        if (is_string($public) && $public !== '') {
            $hosts[] = strtolower($public);
        }
        return array_values(array_unique(array_filter($hosts)));
    }
}
