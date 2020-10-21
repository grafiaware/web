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

/**
 * Description of GetControler
 *
 * @author pes2704
 */
class ComponentController extends LayoutControllerAbstract {
    ### action metody ###############

    public function home(ServerRequestInterface $request) {
        $statusPresentation = $this->statusPresentationRepo->get();
        /** @var MenuRootRepo $menuRootRepo */
        $menuRootRepo = $this->container->get(MenuRootRepo::class);
        /** @var MenuItemRepo $menuItemRepo */
        $menuItemRepo = $this->container->get(MenuItemRepo::class);
        // uid default kořenové položky (z konfigurace)
        $rootName = Configuration::statusPresentationManager()['default_hierarchy_root_component_name'];
        $uidFk = $menuRootRepo->get($rootName)->getUidFk();
        $langCode = $statusPresentation->getLanguage()->getLangCode();
        $rootMenuItem = $menuItemRepo->get($langCode, $uidFk );    // kořen menu
        $statusPresentation->setMenuItem($rootMenuItem);

        $actionComponents = ["content" => $this->getMenuItemComponent($rootMenuItem)];
        return $this->createResponseFromView($request, $this->createView($request, $this->getComponentViews($actionComponents)));    }

    public function item(ServerRequestInterface $request, $langCode, $uid) {
        /** @var HierarchyNodeRepo $menuRepo */
        $menuRepo = $this->container->get(HierarchyNodeRepo::class);
        $menuNode = $menuRepo->get($langCode, $uid);
        if ($menuNode) {
            $menuItem = $menuNode->getMenuItem();
            $this->statusPresentationRepo->get()->setMenuItem($menuItem);
            $actionComponents = ["content" => $this->getMenuItemComponent($menuItem)];
            return $this->createResponseFromView($request, $this->createView($request, $this->getComponentViews($actionComponents)));
        } else {
            // neexistující stránka
            return $this->redirectSeeOther($request, ""); // SeeOther - ->home
        }
    }

    public function last(ServerRequestInterface $request) {
        $actionComponents = ["content" => $this->getMenuItemComponent($this->statusPresentationRepo->get()->getMenuItem())];
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
        return array_merge(
                $actionComponents,
                $this->getGeneratedLayoutComponents(),
                $this->getAuthoredLayoutComnponents(),
                $this->getLayoutComponents(),
//                $this->getEmptyMenuComponents(),
                $this->getMenuComponents()
                );
    }

    /**
     * Vrací view objekt pro zobrazení centrálního obsahu v prostoru pro "content"
     * @return type
     */
    private function getMenuItemComponent(MenuItemInterface $menuItem) {
        // dočasně duplicitní s TemplateController
        $editable = $this->isEditableArticle();
        $menuItemType = $menuItem->getTypeFk();
            switch ($menuItemType) {
//                case 'segment':
//                    if ($editable) {
//                        $content = $this->container->get('article.block.editable');
//                    } else {
//                        $content = $this->container->get('article.block');
//                    }
//                    break;
                case 'segment':
                    if ($editable) {
                        $content = $this->container->get('article.headlined.editable');
                    } else {
                        $content = $this->container->get('article.headlined');
                    }
                    break;
                case 'empty':
                    if ($editable) {
                        $content = $this->container->get(ItemTypeSelectComponent::class);
                    } else {
                        $content = $this->container->get('article.headlined');
                    }
                    break;
                case 'paper':
                    if ($editable) {
                        $content = $this->container->get('article.headlined.editable');
                    } else {
                        $content = $this->container->get('article.headlined');
                    }
                    break;
                case 'redirect':
                    $content = "No content for redirect type.";
                    break;
                case 'root':
                        $content = $this->container->get('article.headlined');
                    break;
                case 'trash':
                        $content = $this->container->get('article.headlined');
                    break;

                default:
                        $content = $this->container->get('article.headlined');
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
            if ($this->isEditableArticle()) {
                $componets = [
                    'menuPresmerovani' => $this->container->get('menu.presmerovani.editable')->setMenuRootName('l'),
                    'menuVodorovne' => $this->container->get('menu.vodorovne.editable')->setMenuRootName('p'),
                    'menuSvisle' => $this->container->get('menu.svisle.editable')->setMenuRootName('s'),
                    'kos' => $this->container->get('menu.kos.editable')->setMenuRootName('trash'), //menu.svisle  //kos
                    'bloky' => $this->container->get('menu.bloky.editable')->setMenuRootName('block'),
                ];
//                return [
//                'menuPresmerovani' => $this->container->get('menu.presmerovani.editable')->setMenuRootName('l'),
//                'menuVodorovne' => $this->container->get('menu.vodorovne.editable')->setMenuRootName('p'),
                ## var a
//                'menuSvisle' => $this->container->get('menu.svisle.editable')->setMenuRootName('$'),
                ## var b
//                'menuSvisle' => $this->container->get('menu.svisle.editable')->setMenuRootName('s'),
//                'bloky' => $this->container->get('menu.bloky.editable')->setMenuRootName('block'), //menu.svisle.editable  //bloky
//                'kos' => $this->container->get('menu.kos')->setMenuRootName('trash'), //menu.svisle  //kos
//                ];
            } else {
                $componets = [
                    'menuPresmerovani' => $this->container->get('menu.presmerovani')->setMenuRootName('l'),
                    'menuVodorovne' => $this->container->get('menu.vodorovne')->setMenuRootName('p'),
                    'menuSvisle' => $this->container->get('menu.svisle')->setMenuRootName('s'),
                    'bloky' => $this->container->get('menu.bloky.editable')->setMenuRootName('block'),
                ];
            }
        } else {
            if ($this->isEditableArticle()) {
                $componets = [
                    'menuPresmerovani' => $this->container->get('menu.presmerovani.editable')->setMenuRootName('l'),
                    'menuVodorovne' => $this->container->get('menu.vodorovne.editable')->setMenuRootName('p'),
                    'menuSvisle' => $this->container->get('menu.svisle.editable')->setMenuRootName('s'),
                    'kos' => $this->container->get('menu.kos.editable')->setMenuRootName('trash'), //menu.svisle  //kos
                ];
            } else {
                $componets = [
                    'menuPresmerovani' => $this->container->get('menu.presmerovani')->setMenuRootName('l'),
                    'menuVodorovne' => $this->container->get('menu.vodorovne')->setMenuRootName('p'),
                    'menuSvisle' => $this->container->get('menu.svisle')->setMenuRootName('s'),
                ];
            }
        }
        return $componets;
    }

    private function getLayoutComponents() {
        if ($this->isEditableLayout() OR $this->isEditableArticle()) {
            $componets = [
                    'aktuality' => $this->container->get('component.headlined.editable')->setComponentName('a1'),
                    'nejblizsiAkce' => $this->container->get('component.headlined.editable')->setComponentName('a2'),
                    'rychleOdkazy' => $this->container->get('component.headlined.editable')->setComponentName('a3'),
                ];
        } else {
            $componets = [
                    'aktuality' => $this->container->get('component.headlined')->setComponentName('a1'),
                    'nejblizsiAkce' => $this->container->get('component.headlined')->setComponentName('a2'),
                    'rychleOdkazy' => $this->container->get('component.headlined')->setComponentName('a3'),
                ];
        }
        return $componets;
    }

    private function getAuthoredLayoutComnponents() {
        if ($this->isEditableLayout()) {
            $componets = [
                    'razitko' => $this->container->get('component.block.editable')->setComponentName('a4'),
                    'socialniSite' => $this->container->get('component.block.editable')->setComponentName('a5'),
                    'mapa' => $this->container->get('component.block.editable')->setComponentName('a6'),
                    'logo' => $this->container->get('component.block.editable')->setComponentName('a7'),
                    'banner' => $this->container->get('component.block.editable')->setComponentName('a8'),
                ];
        } else {
            $componets = [
                    'razitko' => $this->container->get('component.block')->setComponentName('a4'),
                    'socialniSite' => $this->container->get('component.block')->setComponentName('a5'),
                    'mapa' => $this->container->get('component.block')->setComponentName('a6'),
                    'logo' => $this->container->get('component.block')->setComponentName('a7'),
                    'banner' => $this->container->get('component.block')->setComponentName('a8'),
                ];
        }
        return $componets;
    }

}
