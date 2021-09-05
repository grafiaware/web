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
use Component\View\{
    Generated\LanguageSelectComponent,
    Generated\SearchPhraseComponent,
    Generated\SearchResultComponent,
    Generated\ItemTypeSelectComponent,
    Status\LoginComponent, Status\LogoutComponent, Status\UserActionComponent,
    Flash\FlashComponent
};

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
        $homePage = Configuration::pageController()['home_page'];
        switch ($homePage[0]) {
            case 'block':
                $menuItem = $this->getBlockMenuItem($homePage[1]);
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
        $menuItem = $this->getBlockMenuItem($name);
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
    protected function getMenuItem($uid) {
        /** @var MenuItemRepo $menuItemRepo */
        $menuItemRepo = $this->container->get(MenuItemRepo::class);
        $langCode = $this->getPresentationLangCode();
        return $menuItemRepo->get($langCode, $uid);
    }


    protected function getBlockMenuItem($name) {
        /** @var BlockAggregateRepo $blockAggregateRepo */
        $blockAggregateRepo = $this->container->get(BlockAggregateRepo::class);
        $langCode = $this->getPresentationLangCode();
        $blockAggregate = $blockAggregateRepo->getAggregate($langCode, $name);
//        if (!isset($blockAggregate)) {
//            throw new \UnexpectedValueException("Undefined block defined as component with name '$name'.");
//        }
        $menuItem = $blockAggregate ? $blockAggregate->getMenuItem() :null;
        return $menuItem;
    }

#
##### menu komponenty ##############################################################
#
    protected function getMenuComponents() {

        $userActions = $this->statusPresentationRepo->get()->getUserActions();

        $components = [];
        foreach (Configuration::pageController()['menu'] as $menuConf) {
            $this->configMenuComponent($menuConf, $components);
        }
        if ($userActions->presentEditableMenu()) {
            $this->configMenuComponent(Configuration::pageController()['blocks'], $components);
            $this->configMenuComponent(Configuration::pageController()['trash'], $components);

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
        $map = Configuration::pageController()['context_name_to_block_name_map'];
        $componets = [];

        // pro neexistující bloky nedělá nic
        foreach ($map as $variableName => $blockName) {
            $menuItem = $this->getBlockMenuItem($blockName);
            $componets[$variableName] = $this->getMenuItemLoader($menuItem);
        }
        return $componets;
    }

}
