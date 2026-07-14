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
use Red\Service\StaticRegistry\StaticRegistryPushClientInterface;
use Site\ConfigurationCache;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Enum\FlashSeverityEnum;

class StaticRegistryPushAllControler extends FrontControlerAbstract {

    public function __construct(
        StatusSecurityRepo $statusSecurityRepo,
        StatusFlashRepo $statusFlashRepo,
        StatusPresentationRepo $statusPresentationRepo,
        private StaticItemRepo $staticItemRepo,
        private MenuItemRepo $menuItemRepo,
        private StaticRegistryPushClientInterface $pushClient,
    ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
    }

    public function pushAll(ServerRequestInterface $request): ResponseInterface {
        $module = (string) (new RequestParams())->getParam($request, 'module');
        if (!in_array($module, ['events', 'auth'], true)) {
            return $this->createJsonOKResponse(['error' => 'invalid_module'], StatusEnum::_400_BadRequest);
        }

        $baseUrl = $this->resolveBaseUrl($request);
        $siteCode = ConfigurationCache::staticRegistry()['siteCode'];
        $pushed = 0;
        $failed = 0;
        $errors = [];

        foreach ($this->staticItemRepo->findAll() as $static) {
            $menuItem = $this->menuItemRepo->getById((int) $static->getMenuItemIdFk());
            if ($menuItem === null || $menuItem->getApiModuleFk() !== $module) {
                continue;
            }
            if ($menuItem->getApiGeneratorFk() !== ItemApiGeneratorEnum::STATIC_GENERATOR) {
                continue;
            }
            try {
                $this->pushClient->push($module, $static, $siteCode, $baseUrl);
                $pushed++;
            } catch (StaticRegistryPushException $e) {
                $failed++;
                $errors[] = [
                    'menuItemId' => $menuItem->getId(),
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $this->createJsonOKResponse([
            'module' => $module,
            'pushed' => $pushed,
            'failed' => $failed,
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
