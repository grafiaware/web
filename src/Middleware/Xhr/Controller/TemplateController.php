<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Xhr\Controller;

use Psr\Http\Message\ServerRequestInterface;

use Model\Entity\MenuItemInterface;

// komponenty
use Component\View\{
    Generated\LanguageSelectComponent,
    Generated\SearchPhraseComponent,
    Generated\SearchResultComponent,
    Generated\ItemTypeSelectComponent
};

####################

use Model\Repository\{
    HierarchyNodeRepo, MenuRootRepo, MenuItemRepo
};

use \StatusManager\StatusPresentationManager;

####################
//use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;
//use Pes\View\Recorder\RecorderProvider;
//use Pes\View\Recorder\VariablesUsageRecorder;
//use Pes\View\Recorder\RecordsLogger;


/**
 * Description of GetControler
 *
 * @author pes2704
 */
class TemplateController extends XhrControllerAbstract {

    const DEEAULT_HIERARCHY_ROOT_COMPONENT_NAME = 's';

    ### action metody ###############

    public function home(ServerRequestInterface $request) {
        $statusPresentation = $this->statusPresentationRepo->get();
        /** @var MenuRootRepo $menuRootRepo */
        $menuRootRepo = $this->container->get(MenuRootRepo::class);
        /** @var MenuItemRepo $menuItemRepo */
        $menuItemRepo = $this->container->get(MenuItemRepo::class);
        $uidFk = $menuRootRepo->get(StatusPresentationManager::DEEAULT_HIERARCHY_ROOT_COMPONENT_NAME)->getUidFk();
        $langCode = $statusPresentation->getLanguage()->getLangCode();
        $rootMenuItem = $menuItemRepo->get($langCode, $uidFk );    // kořen menu
        $statusPresentation->setMenuItem($rootMenuItem);

        $this->getMenuItemComponent($rootMenuItem);
        return $this->createResponseFromView($request, $this->createView($request));
    }

    public function template(ServerRequestInterface $request, $langCode, $name) {

    }

    public function rendered(ServerRequestInterface $request, $name) {
        $view = $this->container->get('component.block')->setComponentName($name);
        return $this->createResponseFromView($request, $view);
    }

    public function zasobnik($param) {

        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $componentAggregate = $this->componentAggregateRepo->getAggregate($langCode, $this->componentName);
        $menuItem = $componentAggregate->getMenuItem();  // může být null - neaktivní nebo nektuální item v komponentě
        $paperAggregate = isset($menuItem) ? $this->paperAggregateRepo->getByReference($menuItem->getId()) : null;

        /** @var HierarchyNodeRepo $menuRepo */
        $menuRepo = $this->container->get(HierarchyNodeRepo::class);
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

##### private methods ##############################################################
#
}
