<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Xhr\Controller;

use Site\Configuration;

use Psr\Http\Message\ServerRequestInterface;
use Pes\Http\Request\RequestParams;

use Model\Entity\MenuItemInterface;
use Model\Entity\BlockAggregateInterface;

// komponenty
use Component\View\{
    Generated\LanguageSelectComponent,
    Generated\SearchPhraseComponent,
    Generated\SearchResultComponent,
    Generated\ItemTypeSelectComponent,
    Flash\FlashComponent
};

use \Middleware\Xhr\AppContext;

####################

use Model\Repository\{
    HierarchyAggregateRepo, MenuRootRepo, MenuItemRepo, BlockAggregateRepo
};

use \StatusManager\StatusPresentationManager;

use Pes\Text\Message;

####################
//use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;
//use Pes\View\Recorder\RecorderProvider;
//use Pes\View\Recorder\VariablesUsageRecorder;
//use Pes\View\Recorder\RecordsLogger;
use \Pes\View\ViewFactory;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
class ComponentControler extends XhrControlerAbstract {

    ### action metody ###############
    /**
     * NEPOUŽITO
     * @param ServerRequestInterface $request
     * @return type
     */
    public function home(ServerRequestInterface $request) {
        $statusPresentation = $this->statusPresentationRepo->get();
        /** @var MenuRootRepo $menuRootRepo */
        $menuRootRepo = $this->container->get(MenuRootRepo::class);
        /** @var MenuItemRepo $menuItemRepo */
        $menuItemRepo = $this->container->get(MenuItemRepo::class);
        $uidFk = $menuRootRepo->get(Configuration::statusPresentationManager()['default_hierarchy_root_component_name'])->getUidFk();

        $langCode = $statusPresentation->getLanguage()->getLangCode();
        $rootMenuItem = $menuItemRepo->get($langCode, $uidFk );    // kořen menu
        $statusPresentation->setMenuItem($rootMenuItem);

        $this->getMenuItemComponent($rootMenuItem);
        return $this->createResponseFromView($request, $this->createView($request));
    }

    public function flash(ServerRequestInterface $request) {
        $view = $this->container->get(FlashComponent::class);
        return $this->createResponseFromView($request, $view);
    }

    public function namedItem(ServerRequestInterface $request, $name) {
        $view = $this->getNamedComponent($name);
        return $this->createResponseFromView($request, $view);
    }

    public function presentedItem(ServerRequestInterface $request) {
        // dočasně duplicitní s PageController a XhrControler
        $view = $this->getPresentedComponent();
        return $this->createResponseFromView($request, $view);
    }

    public function serviceComponent(ServerRequestInterface $request, $service) {
        if ($this->container->has($service)) {
            $view = $this->container->get($service);
        } else {
            $view = '';
        }
        return $this->createResponseFromView($request, $view);
    }

    ######################

    private function getNamedComponent($name) {
        if ($this->isEditableLayout()) {
            return $this->container->get('component.named.editable')->setComponentName($name);
        } else {
            return $this->container->get('component.named')->setComponentName($name);
        }
    }


    private function getPresentedComponent() {
        return $this->getMenuItemComponent($this->statusPresentationRepo->get()->getMenuItem());
    }


    /**
     * Vrací view objekt pro zobrazení centrálního obsahu v prostoru pro "content"
     * @return type
     */
    private function getMenuItemComponent(MenuItemInterface $menuItem) {
        // dočasně duplicitní s ComponentControler
        $menuItemType = $menuItem->getTypeFk();
            switch ($menuItemType) {
                case 'segment':
                    if ($this->isEditableArticle()) {
                        $content = $this->container->get('component.presented.editable');
                    } else {
                        $content = $this->container->get('component.presented');
                    }
                    break;
                case 'empty':
                    if ($this->isEditableArticle()) {
                        $content = $this->container->get(ItemTypeSelectComponent::class);
                    } else {
                        $content = '';
                    }
                    break;
                case 'paper':
                    if ($this->isEditableArticle()) {
                        $content = $this->container->get('component.presented.editable');
                    } else {
                        $content = $this->container->get('component.presented');
                    }
                    break;
                case 'redirect':
                    $content = "No content for redirect type.";
                    break;
                case 'root':
                        $content = $this->container->get('component.presented');
                    break;
                case 'trash':
                        $content = $this->container->get('component.presented');
                    break;

                default:
                        $content = $this->container->get('component.presented');
                    break;
            }
        return $content;
    }

    /**
     * NEPOUŽITO
     * @param type $param
     * @return type
     */
    public function zasobnik($param) {

        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $componentAggregate = $this->componentAggregateRepo->getAggregate($langCode, $this->componentName);
        $menuItem = $componentAggregate->getMenuItem();  // může být null - neaktivní nebo nektuální item v komponentě
        $paperAggregate = isset($menuItem) ? $this->paperAggregateRepo->getByReference($menuItem->getId()) : null;

        /** @var HierarchyAggregateRepo $menuRepo */
        $menuRepo = $this->container->get(HierarchyAggregateRepo::class);
        $menuNode = $menuRepo->get($langCode, $uid);
        if ($menuNode) {
            $menuItem = $menuNode->getMenuItem();
            $this->statusPresentationRepo->get()->setMenuItem($menuItem);
            $this->getMenuItemComponent($menuItem);
            return $this->createResponseFromView($request, $this->createView($request));
        } else {
            // neexistující stránka
            return $this->redirectSeeOther($request, ""); // SeeOther - ->home
        }
    }

}
