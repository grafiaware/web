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

####################
//use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use \Pes\View\Renderer\StringRenderer;
use \Pes\View\Renderer\ImplodeRenderer;

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
     * @throws UnexpectedValueException
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

##### private methods ##############################################################
#

    private function createResponseWithItem(ServerRequestInterface $request, MenuItemInterface $menuItem = null) {
        if ($menuItem) {
            $this->setPresentationMenuItem($menuItem);
            $actionComponents = ["content" => $this->resolveMenuItemView($menuItem)];
            $response = $this->createResponseFromView($request, $this->createView($request, $this->getComponentViews($actionComponents)));
        } else {
            // neexistující stránka
            $response = $this->redirectSeeOther($request, ""); // SeeOther - ->home
        }
        return $response;
    }

    private function getComponentViews(array $actionComponents) {
        // POZOR! Nesmí se na stránce vyskytovat dva paper se stejným id v editovatelném režimu. TinyMCE vyrobí dvě hidden proměnné se stejným jménem
        // (odvozeným z id), ukládaný obsah editovatelné položky se neuloží - POST data obsahují prázdný řetězec a dojde potichu ke smazání obsahu v databázi.
        // Příklad: - bloky v editovatelném modu a současně editovatelné menu bloky - v menu bloky vybraný blok je zobrazen editovatelný duplicitně s blokem v layoutu
        //          - dva stené bloky v layoutu - mapa, kontakt v hlavičce i v patičce

        return array_merge(
                $actionComponents,
                $this->getGeneratedLayoutComponents(),
                // full page
                $this->getAuthoredLayoutComponents(),
                // for debug
//                $this->getEmptyMenuComponents(),
                $this->getMenuComponents()
                );
    }

    private function getGeneratedLayoutComponents() {
        return [
            'languageSelect' => $this->container->get(LanguageSelectComponent::class),
            'searchPhrase' => $this->container->get(SearchPhraseComponent::class),
        ];
    }

    private function getMenuComponents() {

        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        $configMenu = Configuration::pageController()['menu'];

        $components = [];
        foreach (Configuration::pageController()['menu'] as $menuConf) {
            $this->configMenuComponent($menuConf, $components);
        }
        if ($userActions->isEditableArticle()) {
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

    private function getAuthoredLayoutComponents() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        $isEditableContent = $userActions->isEditableArticle() OR $userActions->isEditableLayout();

        $map = [
                    'rychleOdkazy' => 'a3',
                    'nejblizsiAkce' => 'a2',
                    'aktuality' => 'a1',
                    'razitko' => 'a4',
                    'socialniSite' => 'a5',
                    'mapa' => 'a6',
                    'logo' => 'a7',
                    'banner' => 'a8',
                ];
        $componets = [];

        // pro neexistující bloky nedělá nic
        foreach ($map as $variableName => $blockName) {
            $menuItem = $this->getBlockMenuItem($blockName);
            if (isset($menuItem)) {
                $componets[$variableName] = $this->resolveMenuItemView($menuItem);
            }
        }
        return $componets;
    }

    private function getMenuItem($uid) {
        /** @var MenuItemRepo $menuItemRepo */
        $menuItemRepo = $this->container->get(MenuItemRepo::class);
        $langCode = $this->getPresentationLangCode();
        return $menuItemRepo->get($langCode, $uid);
    }

    private function getBlockMenuItem($name) {
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

    /**
     * Vrací view objekt pro zobrazení centrálního obsahu v prostoru pro "content"
     * @return type
     */
    private function resolveMenuItemView(MenuItemInterface $menuItem) {

        if (isset($menuItem)) {
            $menuItemType = $menuItem->getTypeFk();
            $content = $this->getAuthoredContentLoadScript($menuItemType, $menuItem);

        } else {
            // například neaktivní, neaktuální menu item
            $content = $this->container->get(View::class)->setRenderer(new StringRenderer());
        }
        return $content;
    }

    /**
     * Vrací view s šablonou obsahující skript pro načtení obsahu na základě reference menuItemId pomocí lazy load requestu a záměnu obsahu elementu v html stránky.
     * Parametr uri je id menuItem, aby nebylo třeba načítat paper zde v kontroleru.
     */


    /**
     *
     * @param type $menuItem
     * @return type
     */
    private function getAuthoredContentLoadScript($menuItemType, $menuItem) {
        if ($menuItemType!='static') {
            $menuItemId = $menuItem->getId();
            // prvek data ''loaderWrapperElementId' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
            $view = $this->container->get(View::class)
                        ->setData([
                            'loaderWrapperElementId' => "paper_for_item_{$menuItemId}_generated_by_{$menuItemType}",
                            'apiUri' => "web/v1/$menuItemType/$menuItemId"
                            ]);
        } else {
            $name = $this->getNameForStaticPage($menuItem);
            $view = $this->container->get(View::class)
                        ->setData([
                            'loaderWrapperElementId' => "paper_for_template_{$name}_generated_by_{$menuItemType}",
                            'apiUri' => "web/v1/$menuItemType/$name"
                            ]);
        }
        $this->setLoadScriptTemplate($view);
        return $view;
    }

    private function setLoadScriptTemplate($view) {
        $view->setTemplate(new PhpTemplate(Configuration::pageController()['templates.loaderElement']));
    }


    private function getNameForStaticPage(MenuItemInterface $menuItem) {
        $menuItemPrettyUri = $menuItem->getPrettyuri();
        if (isset($menuItemPrettyUri) AND $menuItemPrettyUri AND strpos($menuItemPrettyUri, "folded:")===0) {      // EditItemController - line 93
            $name = str_replace('/', '_', str_replace("folded:", "", $menuItemPrettyUri));  // zahodí prefix a nahradí '/' za '_' - recopročně
        } else {
            $name = $this->friendlyUrl($menuItem->getTitle());
        }
        return $name;
    }

    private function friendlyUrl($nadpis) {
        $url = $nadpis;
        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
        return $url;
    }
}
