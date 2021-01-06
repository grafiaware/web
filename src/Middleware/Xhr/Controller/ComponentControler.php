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
    Flash\FlashComponent,
    Authored\Paper\PaperComponentInterface,
    Authored\Paper\NamedComponentInterface
};

// view pro kompilované static obsahy
use Pes\View\Renderer\PhpTemplateRenderer;
use Pes\View\Renderer\StringRenderer;

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

    public function flash(ServerRequestInterface $request) {
        $view = $this->container->get(FlashComponent::class);
        return $this->createResponseFromView($request, $view);
    }

    public function componentItem(ServerRequestInterface $request, $name) {
        $view = $this->getNamedComponent($name);
        return $this->createResponseFromView($request, $view);
    }

    public function item(ServerRequestInterface $request, $langCode, $uid) {
        $view = $this->getItemComponent($langCode, $uid);
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

    public function static(ServerRequestInterface $request, $staticName) {
        $compiledContent=$this->getCompiledContent($staticName);
        return $this->createResponseFromString($request, $compiledContent);
    }

    public function paper(ServerRequestInterface $request, $menuItemId) {
        $queryParams = $request->getQueryParams();
        $editable = array_key_exists('editable', $queryParams) ? (bool) $queryParams['editable'] : false;
        $view = $this->getPaperComponent($menuItemId, $editable);
        return $this->createResponseFromView($request, $view);
    }
    ######################

    /**
     * Vrací přeložený obsah statické šablony. Pokud přeložený obsah neexistuje, přeloží ho, t.j. renderuje statickou šablonu a uloží obsah do složky s přeloženými obsahy.
     *
     * @param type $staticName
     * @return string
     */
    private function getCompiledContent($staticName) {

        $compiledFileName = Configuration::componentControler()['static']."__compiled/".$staticName.".html";
        if (false AND is_readable($compiledFileName)) {
            $compiledContent = file_get_contents($compiledFileName);
        } else {
            $view = new View();
            $view->setRenderer(new PhpTemplateRenderer());
            $view->setTemplate(new PhpTemplate(Configuration::componentControler()['static'].$staticName."/template.php"));
            $compiledContent = $view->getString();
            file_put_contents($compiledFileName, $compiledContent);
        }
        return $compiledContent;
    }

    private function getNamedComponent($name) {
        if ($this->isEditableLayout()) {
            $component = $this->container->get('component.named.editable');
        } else {
            $component = $this->container->get('component.named');
        }
        /** @var NamedComponentInterface $component */
        $component->setComponentName($name);
        return $component;
    }

    private function getItemComponent($langCodeFk, $uidFk) {
        if ($this->isEditableArticle()) {
            $component = $this->container->get('component.item.editable');
        } else {
            $component = $this->container->get('component.item');
        }
        /** @var PaperComponentInterface $component */
        $component->setItemId($langCodeFk, $uidFk);
        return $component;
    }

    private function getPaperComponent($menuItemId, $editable) {
        if ($editable) {
            $component = $this->container->get('component.paper.editable');
        } else {
            $component = $this->container->get('component.paper');
        }
        /** @var PaperComponentInterface $component */
        $component->setItemId($menuItemId);
        return $component;
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
                    if ($this->isEditableLayout()) {
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
            $this->statusPresentationRepo->get()->setHierarchyAggregate($menuItem);
            $this->getMenuItemComponent($menuItem);
            return $this->createResponseFromView($request, $this->createView($request));
        } else {
            // neexistující stránka
            return $this->redirectSeeOther($request, ""); // SeeOther - ->home
        }
    }

}
