<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use FrontControler\PresentationFrontControlerAbstract;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Template\Compiler\TemplateCompilerInterface;

use Psr\Http\Message\ServerRequestInterface;

// konfigurace
use Site\ConfigurationCache;

// dao + repo (driver) + entity
use Red\Model\Dao\Hierarchy\HierarchyDao;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\MenuRootRepo;
use Red\Model\Entity\MenuRootInterface;
use Red\Model\Entity\MenuItemInterface;

// enum
use Red\Model\Enum\AuthoredTypeEnum;
//TODO: oprávnění pro routy
use Access\Enum\RoleEnum;
use Access\Enum\AllowedActionEnum;

// view model
use Red\Component\ViewModel\Content\Authored\Paper\PaperViewModel;
use Red\Component\ViewModel\Content\Authored\Article\ArticleViewModel;
use Red\Component\ViewModel\Content\Authored\Multipage\MultipageViewModel;
use Red\Component\ViewModel\Content\TypeSelect\ItemTypeSelectViewModel;
use Red\Component\ViewModel\Menu\DriverViewModelInterface;

// komponenty
use Red\Component\View\Menu\DriverComponent;

use Red\Component\View\Content\TypeSelect\ItemTypeSelectComponent;
use Red\Component\View\Content\Authored\Paper\PaperComponent;
use Red\Component\View\Content\Authored\Paper\PaperComponentInterface;
use Red\Component\View\Content\Authored\Article\ArticleComponent;
use Red\Component\View\Content\Authored\Article\ArticleComponentInterface;
use Red\Component\View\Content\Authored\Multipage\MultipageComponent;
use Red\Component\View\Content\Authored\Multipage\MultipageComponentInterface;

// service (menu)
use Red\Service\Menu\DriverService;
use Red\Service\Menu\DriverServiceInterface;

// renderery
use Pes\View\Renderer\ImplodeRenderer;
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
class ComponentControler extends PresentationFrontControlerAbstract {

    protected function getActionPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AllowedActionEnum::GET => self::class, AllowedActionEnum::POST => self::class],
            RoleEnum::EDITOR => [AllowedActionEnum::GET => self::class, AllowedActionEnum::POST => self::class],
            RoleEnum::AUTHENTICATED => [AllowedActionEnum::GET => self::class],
            RoleEnum::ANONYMOUS => [AllowedActionEnum::GET => self::class]
        ];
    }
    
    ### action metody ###############

    public function serviceComponent(ServerRequestInterface $request, $name) {
        if($this->isAllowed(AllowedActionEnum::GET)) {
            $service = ConfigurationCache::layoutController()['contextServiceMap'][$name] ?? ConfigurationCache::layoutController()['contextLayoutMap'][$name] ?? null;
            if (!isset($service)) {
                $service = ConfigurationCache::layoutController()['contextLayoutEditableMap'][$name] ?? ConfigurationCache::layoutController()['contextLayoutMap'][$name] ?? null;
            }
            if (isset($service) AND $this->container->has($service)) {
                $view = $this->container->get($service);
            } else {
                $view = $this->errorView($request, 'Component is not in controler configuration of context map.');
            }
        } else {
            $view =  $this->getNonPermittedContentView(AllowedActionEnum::GET, AuthoredTypeEnum::PAPER);
        }
        return $this->createResponseFromView($request, $view);
    }
    
    public function presenteddriver(ServerRequestInterface $request, $uid) {

        /** @var DriverComponent $driver */
        $driver = $this->container->get(DriverComponent::class);
        /** @var DriverServiceInterface $driverService */
        $driverService = $this->container->get(DriverService::class);
        $driverService->completeDriverComponent($driver, $uid);
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

    
    public function empty(ServerRequestInterface $request, $menuItemId) {
        return $this->createResponseFromString($request, '');
    }
    
    public function select(ServerRequestInterface $request, $menuItemId) {
        if($this->isAllowed(AllowedActionEnum::GET)) {
            /** @var ItemTypeSelectViewModel $itemTypeSelectViewModel */
            $itemTypeSelectViewModel = $this->container->get(ItemTypeSelectViewModel::class);
            $itemTypeSelectViewModel->setMenuItemId($menuItemId);
            $view = $this->container->get(ItemTypeSelectComponent::class);
        } else {
            $view =  $this->getNonPermittedContentView(AllowedActionEnum::GET, AuthoredTypeEnum::PAPER);
        }
        return $this->createResponseFromView($request, $view);
    }

    public function paper(ServerRequestInterface $request, $menuItemId) {
        if($this->isAllowed(AllowedActionEnum::GET)) {
            /** @var PaperViewModel $paperViewModel */
            $paperViewModel = $this->container->get(PaperViewModel::class);
            $paperViewModel->setMenuItemId($menuItemId);
            /** @var PaperComponentInterface $view */
            $view = $this->container->get(PaperComponent::class);
        } else {
            $view =  $this->getNonPermittedContentView(AllowedActionEnum::GET, AuthoredTypeEnum::PAPER);
        }
        return $this->createResponseFromView($request, $view);
    }

    public function article(ServerRequestInterface $request, $menuItemId) {
        /** @var ArticleViewModel $viewModel */
        $viewModel = $this->container->get(ArticleViewModel::class);
        $viewModel->setMenuItemId($menuItemId);
        /** @var ArticleComponentInterface $view */
        $view = $this->container->get(ArticleComponent::class);
        return $this->createResponseFromView($request, $view);
    }

    public function multipage(ServerRequestInterface $request, $menuItemId) {
        /** @var MultipageViewModel $viewModel */
        $viewModel = $this->container->get(MultipageViewModel::class);
        $viewModel->setMenuItemId($menuItemId);
        /** @var MultipageComponentInterface $view */
        $view = $this->container->get(MultipageComponent::class);
        return $this->createResponseFromView($request, $view);
    }
###################
    private function errorView(ServerRequestInterface $request, $message = '') {
        $view = $this->container->get(View::class);
        $view->setData([Html::tag('div', ['style'=>'display: none;' ], $message)]);
        $view->setRenderer(new ImplodeRenderer());
        return $view;
    }
}
