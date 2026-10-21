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
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * Plná synchronizace static registry: upsert všech položek z red + smazání orphanů v SQLite.
 *
 * POST /red/v1/static/registry/push-sync     — JSON (skripty / ops)
 * POST /red/v1/static/registry/push-sync-ui  — PRG + flash (admin static stránka)
 * Parametr module=events|auth.
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

    /**
     * JSON odpověď pro programové volání.
     */
    public function pushSync(ServerRequestInterface $request): ResponseInterface {
        $module = (string) (new RequestParams())->getParam($request, 'module');
        if (!$this->isValidModule($module)) {
            return $this->createJsonOKResponse(['error' => 'invalid_module'], StatusEnum::_400_BadRequest);
        }
        return $this->createJsonOKResponse($this->runSync($request, $module));
    }

    /**
     * Form POST z admin stránky: sync + flash + redirect na last GET (PRG).
     */
    public function pushSyncUi(ServerRequestInterface $request): ResponseInterface {
        $module = (string) (new RequestParams())->getParam($request, 'module');
        if (!$this->isValidModule($module)) {
            $this->addFlashMessage('Static registry sync: neplatný modul.', FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }

        $result = $this->runSync($request, $module);
        $failed = (int) $result['pushFailed'] + (int) $result['deleteFailed'];
        $parts = [
            "Sync {$result['module']}:",
            "pushed {$result['pushed']}",
            "deleted {$result['deleted']}",
        ];
        if ($result['pushFailed']) {
            $parts[] = "pushFailed {$result['pushFailed']}";
        }
        if ($result['deleteFailed']) {
            $parts[] = "deleteFailed {$result['deleteFailed']}";
        }
        if ($result['deleteSkipped']) {
            $parts[] = "deleteSkipped: {$result['deleteSkipped']}";
        }

        $severity = ($failed > 0 || $result['deleteSkipped'])
            ? FlashSeverityEnum::WARNING
            : FlashSeverityEnum::SUCCESS;
        $this->addFlashMessage(implode(' ', $parts), $severity);

        return $this->redirectSeeLastGet($request);
    }

    private function isValidModule(string $module): bool {
        return in_array($module, ['events', 'auth'], true);
    }

    /**
     * Server-side sync red → auth|events (upsert + delete orphanů).
     *
     * @return array{
     *     module: string,
     *     pushed: int,
     *     pushFailed: int,
     *     deleted: int,
     *     deleteFailed: int,
     *     deleteSkipped: string|null,
     *     errors: list<array{action: string, menuItemId: int, message: string}>
     * }
     */
    private function runSync(ServerRequestInterface $request, string $module): array {
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

        return [
            'module' => $module,
            'pushed' => $pushed,
            'pushFailed' => $pushFailed,
            'deleted' => $deleted,
            'deleteFailed' => $deleteFailed,
            'deleteSkipped' => $deleteSkipped,
            'errors' => $errors,
        ];
    }

    private function resolveBaseUrl(ServerRequestInterface $request): string {
        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();
        $sp = $this->getUriInfo($request)->getSubdomainPath();
        return "$scheme://$host$sp";
    }
}
