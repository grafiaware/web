<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use FrontControler\PresentationFrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

//TODO: oprávnění pro routy
use Access\Enum\RoleEnum;
use Access\Enum\AccessActionEnum;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Access\AccessPresentationInterface;
use Red\Model\Repository\MenuItemRepoInterface;
use Red\Service\Menu\DriverServiceInterface;

// komponenty
use Red\Component\View\Menu\DriverComponent;
use Red\Component\View\Menu\DriverComponentInterface;

// service (menu)
use Red\Service\Menu\DriverService;

####################

use Pes\Text\Html;

####################
//use Pes\Debug\Timer;
use Pes\View\View;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
class MenuControler extends PresentationFrontControlerAbstract {
    
    private $menuItemRepo;
    private $driverService;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo, 
            StatusFlashRepo $statusFlashRepo, 
            StatusPresentationRepo $statusPresentationRepo, 
            AccessPresentationInterface $accessPresentation,
            MenuItemRepoInterface $menuItemRepo,
            DriverServiceInterface $driverService
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $accessPresentation);
        $this->menuItemRepo = $menuItemRepo;
        $this->driverService = $driverService;
    }
    
    protected function getActionPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessActionEnum::GET => self::class, AccessActionEnum::POST => true],
            RoleEnum::EDITOR => [AccessActionEnum::GET => self::class, AccessActionEnum::POST => true],
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => true],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => true]
        ];
    }
    
    ### action metody ###############
    
    public function presentedDriver(ServerRequestInterface $request, $uid) {
        $driver = $this->createDriver($uid, true);
        $this->setPresentationMenuItem($driver->getData()->getMenuItem());  // driver po kompletaci už má data
        return $this->createStringOKResponseFromView($driver);
    }
    
    public function driver(ServerRequestInterface $request, $uid) {
        $driver = $this->createDriver($uid, false);
        return $this->createStringOKResponseFromView($driver);
    }
    
    private function getMenuItem($uid) {
        return $this->menuItemRepo->get($this->statusPresentationRepo->get()->getLanguage()->getLangCode(), $uid);        
    }    
    
    private function createDriver($uid, $isPresented): DriverComponentInterface {
        $menuItem = $this->getMenuItem($uid);
        try {
            $itemType = $this->driverService->getItemType($uid);
        } catch (Exception $exc) {
            throw $exc;
        }


        /** @var DriverComponent $driver */
        $driver = $this->container->get(DriverComponent::class);
        /** @var DriverServiceInterface $driverService */
        $driverService = $this->container->get(DriverService::class);
        $driverService->completeDriverComponent($driver, $menuItem, $isPresented, $itemType);
        return $driver;
    }
}
