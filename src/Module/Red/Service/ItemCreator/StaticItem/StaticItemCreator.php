<?php

namespace Red\Service\ItemCreator\StaticItem;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\StaticItemClass;
use Red\Model\Repository\StaticItemRepo;
use Red\Service\ItemCreator\ItemCreatorAbstract;
use Red\Service\ItemCreator\ItemCreatorInterface;
use Red\Service\StaticRegistry\Exception\StaticRegistryPushException;
use Red\Service\StaticRegistry\StaticRegistryPushService;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

class StaticItemCreator extends ItemCreatorAbstract implements ItemCreatorInterface {

    protected StaticItemRepo $staticRepo;
    private StaticRegistryPushService $staticRegistryPushService;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            StaticItemRepo $staticRepo,
            StaticRegistryPushService $staticRegistryPushService,
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->staticRepo = $staticRepo;
        $this->staticRegistryPushService = $staticRegistryPushService;
    }

    public function initialize(MenuItemInterface $menuItem): void {  
        $static = new StaticItemClass();
        $static->setCreator($this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName());
        $static->setMenuItemIdFk($menuItem->getId());
        $this->staticRepo->add($static);

        try {
            $this->staticRegistryPushService->pushForStaticItem($menuItem, $static, null);
        } catch (StaticRegistryPushException $e) {
            $this->addFlashMessage(
                'Static metadata nebyla synchronizována: ' . $e->getMessage(),
                FlashSeverityEnum::WARNING
            );
        }
    }
}
