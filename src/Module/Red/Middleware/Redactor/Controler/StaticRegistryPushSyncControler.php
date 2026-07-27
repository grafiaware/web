<?php

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response\StatusEnum;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\StaticItemRepo;
use Red\Service\ItemCreator\Enum\ItemApiGeneratorEnum;
use Red\Service\StaticRegistry\Exception\StaticRegistryPushException;
use Red\Service\StaticRegistry\StaticRegistryListClientInterface;
use Red\Service\StaticRegistry\StaticRegistryPushClientInterface;
use Site\ConfigurationCache;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * Plná synchronizace static registry: upsert všech položek z red + smazání orphanů v SQLite.
 *
 * POST /red/v1/static/registry/push-sync  (parametr module=events|auth)
 * Nahradí dřívější push-all (jen upsert). Použití po nasazení, při obnově SQLite
 * nebo když selhala průběžná sync při add/delete menu položky.
 *
 * @author pes2704
 */
class StaticRegistryPushSyncControler extends FrontControlerAbstract {

    public function __construct(
        StatusSecurityRepo $statusSecurityRepo,
        StatusFlashRepo $statusFlashRepo,
        StatusPresentationRepo $statusPresentationRepo,
        private StaticItemRepo $staticItemRepo,
        private MenuItemRepo $menuItemRepo,
        private StaticRegistryPushClientInterface $pushClient,
        private StaticRegistryListClientInterface $listClient,
    ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
    }

    public function pushSync(ServerRequestInterface $request): ResponseInterface {
        $module = (string) (new RequestParams())->getParam($request, 'module');
        if (!in_array($module, ['events', 'auth'], true)) {
            return $this->createJsonOKResponse(['error' => 'invalid_module'], StatusEnum::_400_BadRequest);
        }

        $baseUrl = $this->resolveBaseUrl($request);
        $siteCode = ConfigurationCache::staticRegistry()['staticRegistry.siteCode'] ?? '';
        $pushed = 0;
        $pushFailed = 0;
        $deleted = 0;
        $deleteFailed = 0;
        $errors = [];
        /** @var array<int, true> $desiredMenuItemIds */
        $desiredMenuItemIds = [];

        foreach ($this->staticItemRepo->findAll() as $static) {
            $menuItem = $this->menuItemRepo->getById((int) $static->getMenuItemIdFk());
            if ($menuItem === null || $menuItem->getApiModuleFk() !== $module) {
                continue;
            }
            if ($menuItem->getApiGeneratorFk() !== ItemApiGeneratorEnum::STATIC_GENERATOR) {
                continue;
            }
            $menuItemId = (int) $menuItem->getId();
            $desiredMenuItemIds[$menuItemId] = true;
            try {
                $this->pushClient->push($module, $static, $siteCode, $baseUrl);
                $pushed++;
            } catch (StaticRegistryPushException $e) {
                $pushFailed++;
                $errors[] = [
                    'action' => 'push',
                    'menuItemId' => $menuItemId,
                    'message' => $e->getMessage(),
                ];
            }
        }

        // Orphans: v remote registry, ale už ne v red — smaž. Při selhání listu nic nemaž (bezpečnost).
        $remote = $this->listClient->fetch($module, $baseUrl);
        $deleteSkipped = null;
        if (!empty($remote['error'])) {
            $deleteSkipped = (string) $remote['error'];
        } else {
            foreach ($remote['items'] as $item) {
                $remoteId = (int) ($item['menuItemId'] ?? 0);
                if ($remoteId <= 0 || isset($desiredMenuItemIds[$remoteId])) {
                    continue;
                }
                try {
                    $this->pushClient->delete($module, $remoteId, $baseUrl);
                    $deleted++;
                } catch (StaticRegistryPushException $e) {
                    $deleteFailed++;
                    $errors[] = [
                        'action' => 'delete',
                        'menuItemId' => $remoteId,
                        'message' => $e->getMessage(),
                    ];
                }
            }
        }

        return $this->createJsonOKResponse([
            'module' => $module,
            'pushed' => $pushed,
            'pushFailed' => $pushFailed,
            'deleted' => $deleted,
            'deleteFailed' => $deleteFailed,
            'deleteSkipped' => $deleteSkipped,
            'errors' => $errors,
        ]);
    }

    private function resolveBaseUrl(ServerRequestInterface $request): string {
        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();
        $sp = $this->getUriInfo($request)->getSubdomainPath();
        return "$scheme://$host$sp";
    }
}
