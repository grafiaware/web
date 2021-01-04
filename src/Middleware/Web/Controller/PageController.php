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
use Model\Entity\BlockAggregateInterface;

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
    MenuItemRepo, BlockAggregateRepo

};

####################
//use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
/**
 * Description of GetControler
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

        $homePage = Configuration::pageControler()['home_page'];
        switch ($homePage[0]) {
            case 'component':
                /** @var BlockAggregateRepo $blockAggregateRepo */
                $blockAggregateRepo = $this->container->get(BlockAggregateRepo::class);
                // jméno default komponenty (z konfigurace)
                $langCode = $statusPresentation->getLanguage()->getLangCode();
                $homeComponentAggregate = $blockAggregateRepo->getAggregate($langCode, $homePage[1]);
                if (!isset($homeComponentAggregate)) {
                    throw new \UnexpectedValueException("Undefined default page (home page) defined as component with name '$homePage[1]'.");
                }

                /** @var MenuItemRepo $menuItemRepo */
                $menuItemRepo = $this->container->get(MenuItemRepo::class);

                $homeMenuItem = $homeComponentAggregate->getMenuItem();
                $homeMenuItemUid = $homeMenuItem->getUidFk();
                $resourceUri = "www/item/$langCode/$homeMenuItemUid";
                break;
            case 'static':
                $resourceUri = "www/item/static/$homePage[1]";
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
            $statusPresentation = $this->statusPresentationRepo->get();
            $statusPresentation->setMenuItem($menuItem);
            $actionComponents = ["content" => $this->getMenuItemComponent($menuItem)];
            return $this->createResponseFromView($request, $this->createView($request, $this->getComponentViews($actionComponents)));
        } else {
            // neexistující stránka
            return $this->redirectSeeOther($request, ""); // SeeOther - ->home
        }
    }
    public function static(ServerRequestInterface $request, $name) {
        $actionComponents = ["content" => $this->getStaticLoadScript($name)];
        return $this->createResponseFromView($request, $this->createView($request, $this->getComponentViews($actionComponents)));
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


    private function createSpeedInfoHtml($duration) {
        ## proměnné pro html
//        $duration['Získání dat z modelu'] = $timer->interval();
//        $duration['Vytvoření view s template'] = $timer->interval();
//        $html = $view->getString();   // vynutí renderování už zde
//        $duration['Renderování template'] = $timer->interval();
//        $this->container->get(RecordsLogger::class)
//                ->logRecords($this->container->get(RecorderProvider::class));
//        $duration['Zápis recordu do logu'] = $timer->interval();
//        $duration['Celkem web middleware: '] = $timer->runtime();
//        #### speed test výsledky jsou viditelné ve firebugu ####
//        $html .= $this->createSpeedInfoHtml($duration);
        $testHtml[] = '<div style="display: none;">';
        foreach ($duration as $message => $interval) {
            $testHtml[] = "<p>$message:$interval</p>";
        }
        $testHtml[] = '</div>';
        return implode(PHP_EOL, $testHtml);
    }

    protected function getComponentViews(array $actionComponents) {
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

    /**
     * Vrací view objekt pro zobrazení centrálního obsahu v prostoru pro "content"
     * @return type
     */
    private function getMenuItemComponent(MenuItemInterface $menuItem) {
        // dočasně duplicitní s ComponentControler
        $menuItemType = $menuItem->getTypeFk();
            switch ($menuItemType) {
                case 'empty':
                    if ($this->isEditableArticle()) {
                        $content = $this->container->get(ItemTypeSelectComponent::class);
                    } else {
                        $content = $this->container->get(View::class);
                    }
                    break;
                case 'generated':
                    $content = $view = $this->container->get(View::class)->setData( "No content for generated type.");
                    break;
                case 'static':
                    $content = $this->getStaticLoadScript($menuItem);
                    break;
                case 'paper':
                    $content = $this->getPaperLoadScript($menuItem);

//                    $content = $this->getPresentedComponentLoadScript($menuItem);
                    break;
                case 'redirect':
                    $content = $view = $this->container->get(View::class)->setData( "No content for redirect type.");
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

    private function getGeneratedLayoutComponents() {
        return [
            'languageSelect' => $this->container->get(LanguageSelectComponent::class),
            'searchPhrase' => $this->container->get(SearchPhraseComponent::class),
        ];
    }

    // pro debug
    private function getEmptyMenuComponents() {
        return [
            'menuPresmerovani' => $this->container->get(View::class),
            'menuVodorovne' => $this->container->get(View::class),
            'menuSvisle' => $this->container->get(View::class),
         ];
    }

    private function getMenuComponents() {
        if ($this->isEditableLayout()) {
            $componets = [
                'menuPresmerovani' => $this->container->get('menu.presmerovani')->setMenuRootName('menu_redirect'),
                'menuVodorovne' => $this->container->get('menu.vodorovne')->setMenuRootName('menu_horizontal'),
                'bloky' => $this->container->get('menu.bloky.editable')->setMenuRootName('blocks')->withTitleItem(true),
                'menuSvisle' => $this->container->get('menu.svisle')->setMenuRootName('menu_vertical'),
                'kos' => $this->container->get('menu.kos.editable')->setMenuRootName('trash')->withTitleItem(true),

            ];
        } elseif ($this->isEditableArticle()) {
            $componets = [
                'menuPresmerovani' => $this->container->get('menu.presmerovani.editable')->setMenuRootName('menu_redirect'),
                'menuVodorovne' => $this->container->get('menu.vodorovne.editable')->setMenuRootName('menu_horizontal'),
                'menuSvisle' => $this->container->get('menu.svisle.editable')->setMenuRootName('menu_vertical'),
                'kos' => $this->container->get('menu.kos.editable')->setMenuRootName('trash')->withTitleItem(true),
            ];
        } else {
            $componets = [
                'menuPresmerovani' => $this->container->get('menu.presmerovani')->setMenuRootName('menu_redirect'),
                'menuVodorovne' => $this->container->get('menu.vodorovne')->setMenuRootName('menu_horizontal'),
                'menuSvisle' => $this->container->get('menu.svisle')->setMenuRootName('menu_vertical'),
            ];
        }
        return $componets;
    }

    private function getAuthoredLayoutComponents() {
        if ($this->isEditableLayout()) {
            $componentService = 'component.named.editable';
        } else {
            $componentService = 'component.named';
        }

        $map = [
                    'aktuality' => 'a1',
                    'nejblizsiAkce' => 'a2',
                    'rychleOdkazy' => 'a3',
                    'razitko' => 'a4',
                    'socialniSite' => 'a5',
//                    'mapa' => 'a6',
//                    'logo' => 'a7',
//                    'banner' => 'a8',
                ];
        $componets = [];
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();

        foreach ($map as $variableName => $blockName) {
            $menuItem = $this->getBlockMenuItem($langCode, $blockName);
            $componets[$variableName] = isset($menuItem) ? $this->getMenuItemComponent($menuItem) : $this->container->get(View::class);  // například neaktivní, neaktuální menu item
//            $componets[$variableName] = $this->container->get($componentService)->setComponentName($blockName);
//            $componets[$variableName] = $this->getNamedComponentLoadScript($blockName);  // záhadně nefunkční (debug) a nesmyslná varianta - opakované requesty load element pro všechny segmenty
        }
        return $componets;
    }

    private function getBlockMenuItem($langCode, $name) {
        /** @var BlockAggregateRepo $blockAggregateRepo */
        $blockAggregateRepo = $this->container->get(BlockAggregateRepo::class);
        // jméno default komponenty (z konfigurace)
        $blockAggregate = $blockAggregateRepo->getAggregate($langCode, $name);
        if (!isset($blockAggregate)) {
            throw new \UnexpectedValueException("Undefined component with name '$name'.");
        }

        return $blockAggregate->getMenuItem();
    }
    private function getPaperLoadScript($menuItem) {
        $menuItemId = $menuItem->getId();

        // prvek data 'name' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM 
        $view = $this->container->get(View::class)
                    ->setData([
                        'name' => "paper_for_item_$menuItemId",
                        'apiUri' => "component/v1/paperbyreference/$menuItemId"
                        ]);
        $this->setLoadScriptViewTemplate($view);
        return $view;
    }

    private function getPresentedComponentLoadScript(MenuItemInterface $menuItem) {
        $langCode = $menuItem->getLangCodeFk();
        $uid = $menuItem->getUidFk();
        $view = $this->container->get(View::class)
                    ->setData([
                        'name' => 'presented',
                        'apiUri' => "component/v1/item/$langCode/$uid"
                        ]);
        $this->setLoadScriptViewTemplate($view);
        return $view;
    }

    private function getNamedComponentLoadScript($componentName) {
        $view = $this->container->get(View::class)
                    ->setData([
                        'name' => $componentName,
                        'apiUri' => "component/v1/nameditem/$componentName"
                        ]);
        $this->setLoadScriptViewTemplate($view);
        return $view;
    }

    private function getStaticLoadScript(MenuItemInterface $menuItem) {
        $name = $this->friendlyUrl($menuItem->getTitle());
        $view = $this->container->get(View::class)
                    ->setData([
                        'name' => $name,
                        'apiUri' => "component/v1/static/$name"
                        ]);
        $this->setLoadScriptViewTemplate($view);
        return $view;
    }

    private function setLoadScriptViewTemplate($view) {
        if ($this->isEditableArticle() OR $this->isEditableLayout()) {
            $view->setTemplate(new PhpTemplate(Configuration::pageControler()['templates.loaderElementEditable']));
        } else {
            $view->setTemplate(new PhpTemplate(Configuration::pageControler()['templates.loaderElement']));
        }
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
