<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Middleware\Page\Controller;

use Psr\Http\Message\ServerRequestInterface;

use Site\Configuration;
use Red\Model\Entity\MenuItemInterface;

// komponenty
use Component\View\Generated\LanguageSelectComponent;
use Component\View\Generated\SearchPhraseComponent;
use Component\View\Generated\SearchResultComponent;
use Component\View\Generated\ItemTypeSelectComponent;
use Component\View\Manage\LoginComponent;
use Component\View\Manage\LogoutComponent;
use Component\View\Manage\UserActionComponent;
use Component\View\Flash\FlashComponent;

####################

use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\BlockAggregateRepo;
use Red\Model\Repository\BlockRepo;
use Red\Model\Entity\BlockAggregateMenuItemInterface;

/**
 * Description of GetController
 *
 * @author pes2704
 */
class PageController extends LayoutControllerAbstract {
    ### action metody ###############

    /**
     * Přesměruje na home stránku. Řídí se konfigurací. Home stránka může být definována jménem komponenty nebo jménem statické stránky nebo
     * identifikátorem uid položky menu (položky hierarchie).
     *
     * @param ServerRequestInterface $request
     * @return type
     * @throws \UnexpectedValueException
     */
    public function home(ServerRequestInterface $request) {
        $homePage = Configuration::layoutController()['home_page'];
        switch ($homePage[0]) {
            case 'block':
                $menuItem = $this->getMenuItemForBlock($homePage[1]);
                if (!isset($menuItem)) {
                    throw new \UnexpectedValueException("Undefined menu item for default page (home page) defined as component with name '$homePage[1]'.");
                }
                break;
            case 'item':
                $menuItem = $this->getMenuItem($homePage[1]);
                if (!isset($menuItem)) {
                    throw new UnexpectedValueException("Undefined default page (home page) defined as static with name '$homePage[1]'.");
                }
                break;
            default:
                throw new UnexpectedValueException("Unknown home page type in configuration. Type: '$homePage[0]'.");
                break;
        }
        return $this->createResponseWithItem($request, $menuItem);
    }

    public function item(ServerRequestInterface $request, $uid) {
        $menuItem = $this->getMenuItem($uid);
        return $this->createResponseWithItem($request, $menuItem);
    }

    public function block(ServerRequestInterface $request, $name) {
        $menuItem = $this->getMenuItemForBlock($name);
        return $this->createResponseWithItem($request, $menuItem);
    }

    public function subitem(ServerRequestInterface $request, $uid) {
        /** @var MenuItemRepo $menuItemRepo */
        $menuItemRepo = $this->container->get(MenuItemRepo::class);
        $langCode = $this->getPresentationLangCode();
        $menuItem = $menuItemRepo->getOutOfContext($langCode, $uid);
        return $this->createResponseWithItem($request, $menuItem);
    }

    public function searchResult(ServerRequestInterface $request) {
        // TODO tady je nějaký zmatek
        /** @var SearchResultComponent $component */
        $component = $this->container->get(SearchResultComponent::class);
        $key = $request->getQueryParams()['klic'];
        $actionComponents = ["content" => $component->setSearch($key)];
        return $this->createResponseFromView($request, $this->createView($request, $this->getComponentViews($actionComponents)));
    }

#### get menu item z repository ###########################################################################
#
    /**
     * Podle hierarchy uid a aktuálního jazyka prezentace vrací menuItem nebo null
     *
     * @param string $uid
     * @return MenuItemInterface|null
     */
    protected function getMenuItem($uid): ?MenuItemInterface {
        /** @var MenuItemRepo $menuItemRepo */
        $menuItemRepo = $this->container->get(MenuItemRepo::class);
        return $menuItemRepo->get($this->getPresentationLangCode(), $uid);
    }

    /**
     * Podle jména bloku a aktuálního jazyka prezentace vrací menuItem nebo null
     *
     * @param string $name
     * @return MenuItemInterface|null
     */
    protected function getMenuItemForBlock($name): ?MenuItemInterface {
        /** @var BlockAggregateRepo $blockAggregateRepo */
        $blockAggregateRepo = $this->container->get(BlockAggregateRepo::class);
        $blockAggregate = $blockAggregateRepo->getAggregate($this->getPresentationLangCode(), $name);
//        if (!isset($blockAggregate)) {
//            throw new \UnexpectedValueException("Undefined block defined as component with name '$name'.");
//        }
        return isset($blockAggregate) ? $blockAggregate->getMenuItem() : null;  // není blok nebo není publikovaný&aktivní item
    }

#
##### menu komponenty ##############################################################
#
    protected function getMenuComponents() {

        $userActions = $this->statusPresentationRepo->get()->getUserActions();

        $components = [];
        foreach (Configuration::layoutController()['menu'] as $menuConf) {
            $this->configMenuComponent($menuConf, $components);
        }
        if ($userActions->presentEditableMenu()) {
            $this->configMenuComponent(Configuration::layoutController()['blocks'], $components);
            $this->configMenuComponent(Configuration::layoutController()['trash'], $components);

        }

        return $components;
    }

    private function configMenuComponent($menuConf, &$componets): void {
                $componets[$menuConf['context_name']] = $this->container->get($menuConf['service_name'])
                        ->setMenuRootName($menuConf['root_name'])
                        ->withTitleItem($menuConf['with_title']);
    }

#
#### menu item loadery pro bloky layoutu #########################################################################
#

    protected function getAuthoredLayoutBlockLoaders() {
        $map = Configuration::layoutController()['context_name_to_block_name_map'];
        $componets = [];

        // pro neexistující bloky nedělá nic
        foreach ($map as $variableName => $blockName) {
            $menuItem = $this->getMenuItemForBlock($blockName);
            $componets[$variableName] = $this->getMenuItemLoader($menuItem);
        }
        return $componets;
    }

}
