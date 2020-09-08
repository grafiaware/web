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
    Generated\ItemTypeSelectComponent,
    Flash\FlashComponent
};

use \Middleware\Xhr\AppContext;

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
use \Pes\View\ViewFactory;

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

    public function template(ServerRequestInterface $request, $name) {
        return $this->createResponseFromString($request, file_get_contents(AppContext::getTinyPublicDirectory()."paper/".$name.".html"));
    }

    public function flash(ServerRequestInterface $request) {
        $view = $this->container->get(FlashComponent::class);
        return $this->createResponseFromView($request, $view);
    }

    public function namedPaper(ServerRequestInterface $request, $name) {
        $view = $this->container->get('component.block')->setComponentName($name);
        return $this->createResponseFromView($request, $view);
    }

    public function presentedPaper(ServerRequestInterface $request) {
        // dočasně duplicitní s ComponentController
        $menuItem = $this->statusPresentationRepo->get()->getMenuItem();
        $editable = $this->isEditableArticle();
        $menuItemType = $menuItem->getTypeFk();
            switch ($menuItemType) {
                case 'segment':
                    if ($editable) {
                        $view = $this->container->get('article.block.editable');
                    } else {
                        $view = $this->container->get('article.block');
                    }
                    break;
                case 'empty':
                    if ($editable) {
                        $view = $this->container->get(ItemTypeSelectComponent::class);
                    } else {
                        $view = $this->container->get('article.headlined');
                    }
                    break;
                case 'paper':
                    if ($editable) {
                        $view = $this->container->get('article.headlined.editable');
                    } else {
                        $view = $this->container->get('article.headlined');
                    }
                    break;
                case 'redirect':
                    $view = "No content for redirect type.";
                    break;
                case 'root':
                        $view = $this->container->get('article.headlined');
                    break;
                case 'trash':
                        $view = $this->container->get('article.headlined');
                    break;

                default:
                        $view = $this->container->get('article.headlined');
                    break;
            }
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
