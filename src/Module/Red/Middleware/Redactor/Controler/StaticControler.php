<?php

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;
use Pes\Http\Request\RequestParams;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Red\Model\Entity\StaticItemInterface;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\StaticItemRepo;
use Red\Service\StaticRegistry\Exception\StaticRegistryPushException;
use Red\Service\StaticRegistry\StaticRegistryPushService;
use StaticRegistry\Service\StaticTemplateLocation;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * POST update path/template static položky v red DB.
 *
 * Po zápisu do red tabulky static pushne metadata na auth/events registry,
 * pokud api_module_fk položky je events nebo auth.
 *
 * @author pes2704
 */
class StaticControler extends FrontControlerAbstract {

    const PATH_VAR_NAME = "path";
    const TEMPLATE_VAR_NAME = "template";
    /** JSON {"path","template"} z jednoho selectu v editoru. */
    const LOCATION_VAR_NAME = "staticLocation";
    
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            private StaticItemRepo $staticRepo,
            private MenuItemRepo $menuItemRepo,
            private StaticRegistryPushService $staticRegistryPushService,
    ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
    }

    /**
     * POST /red/v1/static/:staticId
     *
     * Nejdřív flush do MySQL (sloupec updated nastaví server), pak push do SQLite
     * s časem už uloženým v řádku. Push před flushem posílal staré updated
     * a SQLite zápis zahodil (lokální updated >= příchozí).
     */
    public function update(ServerRequestInterface $request, $staticId): ResponseInterface {
        $location = $this->readLocation($request);
        if ($location === null) {
            $this->addFlashMessage('Neplatný výběr šablony. Path a template se nezměnily.', FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        $static = $this->staticRepo->get($staticId);
        $static->setPath($location['path']);
        $static->setTemplate($location['template']);
        $static->setCreator($this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName());

        $this->staticRepo->flush();
        $stored = $this->staticRepo->get($staticId) ?? $static;

        // Sync na remote registry — selhání push neblokuje editaci (jen flash warning)
        $this->pushRemoteRegistry($request, $stored);
        
        return $this->redirectSeeLastGet($request);
    }

    /**
     * Select editoru posílá obě hodnoty v staticLocation.
     * Textová pole posílají path a template zvlášť.
     *
     * @return array{path: ?string, template: ?string}|null null = neplatný select, nic nezapisovat
     */
    private function readLocation(ServerRequestInterface $request): ?array {
        $params = new RequestParams();
        $encoded = $params->getParam($request, self::LOCATION_VAR_NAME);
        if (is_string($encoded) && $encoded !== '') {
            $decoded = StaticTemplateLocation::decode($encoded);
            if ($decoded === null) {
                return null;
            }
            return $decoded;
        }
        return [
            'path' => $params->getParam($request, self::PATH_VAR_NAME),
            'template' => $params->getParam($request, self::TEMPLATE_VAR_NAME),
        ];
    }

    private function pushRemoteRegistry(ServerRequestInterface $request, StaticItemInterface $static): void {
        $menuItem = $this->menuItemRepo->getById((int) $static->getMenuItemIdFk());
        if ($menuItem === null) {
            return;
        }
        try {
            $this->staticRegistryPushService->pushForStaticItem(
                $menuItem,
                $static,
                $this->resolveBaseUrl($request)
            );
        } catch (StaticRegistryPushException $e) {
            $this->addFlashMessage(
                'Static metadata nebyla synchronizována: ' . $e->getMessage(),
                FlashSeverityEnum::WARNING
            );
        }
    }

    private function resolveBaseUrl(ServerRequestInterface $request): string {
        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();
        $sp = $this->getUriInfo($request)->getSubdomainPath();
        return "$scheme://$host$sp";
    }
}
