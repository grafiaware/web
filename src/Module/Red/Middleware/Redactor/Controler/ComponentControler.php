<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use FrontControler\PresentationFrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

// konfigurace
use Site\ConfigurationCache;

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

use Red\Component\View\Content\TypeSelect\ItemTypeSelectComponent;
use Red\Component\View\Content\Authored\Paper\PaperComponent;
use Red\Component\View\Content\Authored\Paper\PaperComponentInterface;
use Red\Component\View\Content\Authored\Article\ArticleComponent;
use Red\Component\View\Content\Authored\Article\ArticleComponentInterface;
use Red\Component\View\Content\Authored\Multipage\MultipageComponent;
use Red\Component\View\Content\Authored\Multipage\MultipageComponentInterface;

// renderery
use Pes\View\Renderer\ImplodeRenderer;

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
            RoleEnum::SUPERVISOR => [AllowedActionEnum::GET => self::class],
            RoleEnum::EDITOR => [AllowedActionEnum::GET => self::class],
            RoleEnum::AUTHENTICATED => [AllowedActionEnum::GET => self::class],
            RoleEnum::ANONYMOUS => [AllowedActionEnum::GET => self::class]
        ];
    }
    
    ### action metody ###############

    public function serviceComponent(ServerRequestInterface $request, $name) {
        if($this->isAllowed(AllowedActionEnum::GET)) {
            if (array_key_exists($name, ConfigurationCache::layoutController()['contextServiceMap'])) {
                $service = reset(ConfigurationCache::layoutController()['contextServiceMap'][$name]) ?? null;
            } else {
                $service = ConfigurationCache::layoutController()['contextLayoutMap'][$name] ?? ConfigurationCache::layoutController()['contextLayoutEditableMap'][$name] ?? null;
            }
            if (!isset($service)) {
                $view = $this->errorView($request, "Component $name undefined in configuration of context layout maps or context service map.");
            }
            if($this->container->has($service)) {
                $view = $this->container->get($service);
            } else {
                $view = $this->errorView($request, "Component $service is not defined (configured) in container.");                    
            }
        } else {
            $view =  $this->getNonPermittedContentView(AllowedActionEnum::GET, AuthoredTypeEnum::PAPER);
        }
        return $this->createStringOKResponseFromView($view);
    }

    public function root(ServerRequestInterface $request, $menuItemId) {
        return $this->createStringOKResponse('');
    }
    
    public function empty(ServerRequestInterface $request, $menuItemId) {
        return $this->createStringOKResponse('');
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
        return $this->createStringOKResponseFromView($view);
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
        return $this->createStringOKResponseFromView($view);
    }

    public function article(ServerRequestInterface $request, $menuItemId) {
        if($this->isAllowed(AllowedActionEnum::GET)) {
            /** @var ArticleViewModel $viewModel */
            $viewModel = $this->container->get(ArticleViewModel::class);
            $viewModel->setMenuItemId($menuItemId);
            /** @var ArticleComponentInterface $view */
            $view = $this->container->get(ArticleComponent::class);
        } else {
            $view =  $this->getNonPermittedContentView(AllowedActionEnum::GET, AuthoredTypeEnum::PAPER);
        }
        return $this->createStringOKResponseFromView($view);
    }

    public function multipage(ServerRequestInterface $request, $menuItemId) {
        if($this->isAllowed(AllowedActionEnum::GET)) {
            /** @var MultipageViewModel $viewModel */
            $viewModel = $this->container->get(MultipageViewModel::class);
            $viewModel->setMenuItemId($menuItemId);
            /** @var MultipageComponentInterface $view */
            $view = $this->container->get(MultipageComponent::class);
        } else {
            $view =  $this->getNonPermittedContentView(AllowedActionEnum::GET, AuthoredTypeEnum::PAPER);
        }
        return $this->createStringOKResponseFromView($view);
    }
    
###################
    private function errorView(ServerRequestInterface $request, $message = '') {
        $view = $this->container->get(View::class);
        $view->setData([Html::tag('div', ['style'=>'display: none;' ], $message)]);
        $view->setRenderer(new ImplodeRenderer());
        return $view;
    }
}
