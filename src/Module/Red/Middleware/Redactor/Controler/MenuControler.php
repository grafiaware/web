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

// komponenty
use Red\Component\View\Menu\DriverComponent;
use Red\Component\View\Menu\DriverComponentInterface;

// service (menu)
use Red\Service\Menu\DriverService;
use Red\Service\Menu\DriverServiceInterface;

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
    
    private function createDriver($uid, $isPresented): DriverComponentInterface {
        /** @var DriverComponent $driver */
        $driver = $this->container->get(DriverComponent::class);
        /** @var DriverServiceInterface $driverService */
        $driverService = $this->container->get(DriverService::class);
        $driverService->completeDriverComponent($driver, $uid, $isPresented);
        return $driver;
    }
}
