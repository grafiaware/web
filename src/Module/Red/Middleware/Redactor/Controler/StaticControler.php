<?php

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;
use Pes\Http\Request\RequestParams;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\StaticItemRepo;
use Red\Service\StaticRegistry\Exception\StaticRegistryPushException;
use Red\Service\StaticRegistry\StaticRegistryPushService;
use Site\ConfigurationCache;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

class StaticControler extends FrontControlerAbstract {

    const PATH_VAR_NAME = "path";
    const TEMPLATE_VAR_NAME = "template";
    
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

    public function update(ServerRequestInterface $request, $staticId): ResponseInterface {
        $path = (new RequestParams())->getParam($request, self::PATH_VAR_NAME);
        $template = (new RequestParams())->getParam($request, self::TEMPLATE_VAR_NAME);        
        $static = $this->staticRepo->get($staticId);
        $static->setPath($path);
        $static->setTemplate($template);
        $static->setCreator($this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName());

        $this->pushRemoteRegistry($request, $static);
        
        return $this->redirectSeeLastGet($request);
    }

    private function pushRemoteRegistry(ServerRequestInterface $request, \Red\Model\Entity\StaticItemInterface $static): void {
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
