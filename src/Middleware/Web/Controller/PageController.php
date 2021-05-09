<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Web\Controller;

use Psr\Http\Message\ServerRequestInterface;

use Site\Configuration;
use Model\Entity\MenuItemInterface;

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

use Model\Repository\{
    MenuItemRepo, BlockAggregateRepo, BlockRepo
};
use Model\Entity\BlockAggregateMenuItemInterface;

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
        $statusPresentation = $this->statusPresentationRepo->get();

        $homePage = Configuration::pageController()['home_page'];
        switch ($homePage[0]) {
            case 'block':
                /** @var BlockAggregateRepo $blockAggregateRepo */
                $blockAggregateRepo = $this->container->get(BlockAggregateRepo::class);
                // jméno default komponenty (z konfigurace)
                $langCode = $statusPresentation->getLanguage()->getLangCode();
                $homeComponentAggregate = $blockAggregateRepo->getAggregate($langCode, $homePage[1]);
                if (!isset($homeComponentAggregate)) {
                    throw new \UnexpectedValueException("Undefined default page (home page) defined as component with name '$homePage[1]'.");
                }
                $homeMenuItem = $homeComponentAggregate->getMenuItem();
                if (!isset($homeMenuItem)) {
                    throw new \UnexpectedValueException("Undefined menu item with uid: '{$homeComponentAggregate->getUidFk()}'for default page (home page) defined as component with name '$homePage[1]'.");
                }
                /** @var MenuItemRepo $menuItemRepo */
                $menuItemRepo = $this->container->get(MenuItemRepo::class);

                $homeMenuItemUid = $homeMenuItem->getUidFk();
                $resourceUri = "www/item/$langCode/$homeMenuItemUid";
                break;
            case 'static':
                $resourceUri = "www/block/$homePage[1]";
                break;
            case 'item':
                /** @var MenuItemRepo $menuItemRepo */
                $menuItemRepo = $this->container->get(MenuItemRepo::class);
                // jméno default komponenty (z konfigurace)
                $langCode = $statusPresentation->getLanguage()->getLangCode();
                $homeMenuItem = $menuItemRepo->get($langCode, $homePage[1]);
                if (!isset($homeMenuItem)) {
                    throw new UnexpectedValueException("Undefined default page (home page) defined as static with name '$homePage[1]'.");
                }
                $homeMenuItemUid = $homeMenuItem->getUidFk();
                $resourceUri = "www/item/$langCode/$homeMenuItemUid";
                break;
            default:
                throw new UnexpectedValueException("Unknown home page type in configuration. Type: '$homePage[0]'.");
                break;
        }

        $statusPresentation->setLastGetResourcePath($resourceUri);
        $statusPresentation->setMenuItem($homeMenuItem);
        return $this->redirectSeeOther($request, $resourceUri); // 303 See Other
    }

    public function item(ServerRequestInterface $request, $langCode, $uid) {
        /** @var MenuItemRepo $menuItemRepo */
        $menuItemRepo = $this->container->get(MenuItemRepo::class);
        $menuItem = $menuItemRepo->get($langCode, $uid);
        if ($menuItem) {
            $this->setPresentationMenuItem($menuItem);
            $actionComponents = ["content" => $this->resolveMenuItemView($menuItem)];
            return $this->createResponseFromView($request, $this->createView($request, $this->getComponentViews($actionComponents)));
        } else {
            // neexistující stránka
            return $this->redirectSeeOther($request, ""); // SeeOther - ->home
        }
    }

    public function block(ServerRequestInterface $request, $name) {
        $menuItem = $this->getBlockMenuItem($name);
        if ($menuItem) {
            $this->setPresentationMenuItem($menuItem);
            $actionComponents = ["content" => $this->resolveMenuItemView($menuItem)];
            return $this->createResponseFromView($request, $this->createView($request, $this->getComponentViews($actionComponents)));
        }
        // neexistující stránka
        return $this->redirectSeeOther($request, ""); // SeeOther - ->home
    }

    public function subitem(ServerRequestInterface $request, $langCode, $uid) {
        /** @var MenuItemRepo $menuItemRepo */
        $menuItemRepo = $this->container->get(MenuItemRepo::class);
        $menuItem = $menuItemRepo->getOutOfContext($langCode, $uid);
        if ($menuItem) {
            $this->setPresentationMenuItem($menuItem);
            $actionComponents = ["content" => $this->resolveMenuItemView($menuItem)];
            return $this->createResponseFromView($request, $this->createView($request, $this->getComponentViews($actionComponents)));
        } else {
            // neexistující stránka
            return $this->redirectSeeOther($request, ""); // SeeOther - ->home
        }
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

        if ($isEditableContent) {
            $componentService = 'component.named.editable';
        } else {
            $componentService = 'component.named';
        }

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
        foreach ($map as $variableName => $blockName) {
            $menuItem = $this->getBlockMenuItem($blockName);
            $componets[$variableName] = $this->resolveMenuItemView($menuItem);
//            $componets[$variableName] = $this->container->get($componentService)->setComponentName($blockName);
        }
        return $componets;
    }

    private function getBlockMenuItem($name) {
        $statusPresentation = $this->statusPresentationRepo->get();

        /** @var BlockAggregateRepo $blockAggregateRepo */
        $blockAggregateRepo = $this->container->get(BlockAggregateRepo::class);
        // jméno default komponenty (z konfigurace)
        $langCode = $statusPresentation->getLanguage()->getLangCode();
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
    private function resolveMenuItemView(MenuItemInterface $menuItem = null) {
        if (isset($menuItem)) {
            // dočasně duplicitní s ComponentController
            $userActions = $this->statusSecurityRepo->get()->getUserActions();
            $isEditableContent = $userActions->isEditableArticle() OR $userActions->isEditableLayout();
            $menuItemType = $menuItem->getTypeFk();

            switch ($menuItemType) {
                case 'empty':
                    if ($isEditableContent) {
                        $content = $this->container->get(ItemTypeSelectComponent::class);
                    } else {
                        $content = $this->container->get(View::class)->setData(["Empty item."])->setRenderer(new ImplodeRenderer());
                    }
                    break;
                case 'generated':
                    $content = $view = $this->container->get(View::class)->setData( "No content for generated type.")->setRenderer(new StringRenderer());
                    break;
                case 'static':
                    if ($isEditableContent) {
                        $content = $this->getStaticEditableLoadScript($menuItem);
                    } else {
                        $content = $this->getStaticLoadScript($menuItem);
                    }
                    break;
                case 'paper':
                    if ($isEditableContent) {
                        $content = $this->getPaperEditableLoadScript($menuItem);
                    } else {
                        $content = $this->getPaperLoadScript($menuItem);
                    }

//                    $content = $this->getPresentedComponentLoadScript($menuItem);
                    break;
                case 'redirect':
                    $content = $view = $this->container->get(View::class)->setData( "No content for redirect type.")->setRenderer(new StringRenderer());
                    break;
                case 'root':
                        $content = $this->container->get(View::class)->setData( "root")->setRenderer(new StringRenderer());
                    break;
                case 'trash':
                        $content = $this->container->get(View::class)->setData( "trash")->setRenderer(new StringRenderer());
                    break;

                default:
                        $content = $this->container->get('component.presented');
                    break;
            }
        } else {
            // například neaktivní, neaktuální menu item
            $content = $this->container->get(View::class)->setRenderer(new StringRenderer());
        }
        return $content;
    }

    /**
     * Vrací view s šablonou obsahující skript pro načtení paperu na základě reference menuItemId pomocí lazy load requestu a záměnu obsahu elementu v html stránky.
     * Parametr uri je id menuItem, aby nebylo třeba načítat paper zde v kontroleru.
     * Lazy načítaný paper musí být v modu editable, pokud je nastaven uri query parametr editable=1.
     *
     * @param type $menuItem
     * @param bool $editable Pokud je true, přidá k uri query parametr editable=1
     * @return type
     */
    private function getPaperLoadScript($menuItem) {
        $menuItemId = $menuItem->getId();
        // prvek data 'name' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
        $view = $this->container->get(View::class)
                    ->setData([
                        'name' => "paper_for_item_$menuItemId",
                        'apiUri' => "component/v1/itempaper/$menuItemId"
                        ]);
        $this->setLoadScriptTemplate($view);
        return $view;
    }
    private function getPaperEditableLoadScript($menuItem) {
        $menuItemId = $menuItem->getId();
        // prvek data 'name' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
        $view = $this->container->get(View::class)
                    ->setData([
                        'name' => "paper_for_item_$menuItemId",
                        'apiUri' => "component/v1/itempapereditable/$menuItemId"
                        ]);
        $this->setLoadEditableScriptTemplate($view);
        return $view;
    }

    private function getStaticLoadScript(MenuItemInterface $menuItem) {
        $name = $this->getNameForStaticPage($menuItem);
        $view = $this->container->get(View::class)
                    ->setData([
                        'name' => $name,
                        'apiUri' => "component/v1/static/$name"
                        ]);
        $this->setLoadScriptTemplate($view);
        return $view;
    }

    private function getStaticEditableLoadScript(MenuItemInterface $menuItem) {
        $name = $this->getNameForStaticPage($menuItem);
        $view = $this->container->get(View::class)
                    ->setData([
                        'name' => $name,
                        'apiUri' => "component/v1/static/$name"
                        ]);
        $this->setLoadEditableScriptTemplate($view);
        return $view;
    }

    private function setLoadScriptTemplate($view) {
        $view->setTemplate(new PhpTemplate(Configuration::pageController()['templates.loaderElement']));
    }

    private function setLoadEditableScriptTemplate($view) {
        $view->setTemplate(new PhpTemplate(Configuration::pageController()['templates.loaderElementEditable']));
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
