<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use FrontControler\PresentationFrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

// enum
use Red\Model\Enum\AuthoredTypeEnum;
//TODO: oprávnění pro routy
use Access\Enum\RoleEnum;
use Access\Enum\AllowedActionEnum;

// komponenty
use Red\Component\View\Menu\DriverComponent;
use Red\Component\View\Menu\DriverComponentInterface;
use Red\Component\ViewModel\Menu\DriverViewModelInterface;
use Red\Component\View\Menu\DriverButtonsComponent;

// service (menu)
use Red\Service\Menu\DriverService;
use Red\Service\Menu\DriverServiceInterface;

// renderery
use Red\Component\Renderer\Html\Menu\DriverRenderer;
use Red\Component\Renderer\Html\Menu\DriverRendererEditable;

####################

use Pes\Text\Html;

####################
//use Pes\Debug\Timer;
use Pes\View\View;

/**
 * Description of ComponentController
 *
 * @author pes2704
 */
class MenuControler extends PresentationFrontControlerAbstract {

    protected function getActionPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AllowedActionEnum::GET => self::class, AllowedActionEnum::POST => self::class],
            RoleEnum::EDITOR => [AllowedActionEnum::GET => self::class, AllowedActionEnum::POST => self::class],
            RoleEnum::AUTHENTICATED => [AllowedActionEnum::GET => self::class],
            RoleEnum::ANONYMOUS => [AllowedActionEnum::GET => self::class]
        ];
    }
    
    ### action metody ###############
    
    public function presenteddriver(ServerRequestInterface $request, $uid) {

        /** @var DriverComponent $driver */
        $driver = $this->container->get(DriverComponent::class);
        /** @var DriverServiceInterface $driverService */
        $driverService = $this->container->get(DriverService::class);
        $driverService->completeDriverComponent($driver, $uid);
        /** @var DriverViewModelInterface $driverViewModel */
        $driverViewModel = $driver->getData();
        if ($driverViewModel->isPresented()) {
            $driverButtons = $this->container->get(DriverButtonsComponent::class);
            $driver->appendComponentView($driverButtons, DriverComponentInterface::DRIVER_BUTTONS);// DriverButtonsComponent je typu InheritData - tímto vložením dědí DriverViewModel
        }
        
        $this->setPresentationMenuItem($driver->getData()->getMenuItem());  // driver po kompletaci už má data
        
        return $this->createResponseFromView($request, $driver);
    }
    
    public function driver(ServerRequestInterface $request, $uid) {

        /** @var DriverComponent $driver */
        $driver = $this->container->get(DriverComponent::class);
        /** @var DriverServiceInterface $driverService */
        $driverService = $this->container->get(DriverService::class);
        $driverService->completeDriverComponent($driver, $uid);
        return $this->createResponseFromView($request, $driver);
    }
}
