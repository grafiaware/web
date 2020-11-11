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

use Middleware\Login\Controller\LoginLogoutController;

####################

use Model\Repository\{
    HierarchyAggregateRepo, MenuRootRepo, MenuItemRepo, BlockAggregateRepo
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

    public function home(ServerRequestInterface $request) {
        return $this->static($request, 'uvod');
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
        /** @var HierarchyAggregateRepo $menuRepo */
        $menuRepo = $this->container->get(HierarchyAggregateRepo::class);
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
    public function static(ServerRequestInterface $request, $name) {
            $actionComponents = ["content" => $this->getStaticLoadScript($name)];
            return $this->createResponseFromView($request, $this->createView($request, $this->getComponentViews($actionComponents)));
    }

    public function last(ServerRequestInterface $request) {
        $actionComponents = ["content" => $this->getPresentedComponent()];
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
        if (false) {
        // full page
        return array_merge(
                $actionComponents,
                $this->getGeneratedLayoutComponents(),
                // full page
                $this->getAuthoredLayoutComnponents(),
                // for debug
//                $this->getEmptyMenuComponents(),
                $this->getMenuComponents()
                );
        } else {
        // xhr components load
        return array_merge(
                $actionComponents,
                $this->getGeneratedLayoutComponents(),
                $this->getXhrAuthoredLayoutComnponents(),
                // for debug
//                $this->getEmptyMenuComponents(),
                $this->getMenuComponents()
                );
        }
    }

    private function getNamedComponent($name) {
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        /** @var BlockAggregateRepo $componentAggRepo */
        $componentAggRepo = $this->container->get(BlockAggregateRepo::class);
        $componentAggregate = $componentAggRepo->getAggregate($langCode, $name);   // pozor - get() je metoda ComponentRepo
        /** @var BlockAggregateInterface $componentAggregate */
        if (isset($componentAggregate)) {
            return $this->getMenuItemComponent($componentAggregate->getMenuItem());
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
                'menuSvisle' => $this->container->get('menu.svisle')->setMenuRootName('menu_vertical'),
            ];
//                return [
//                'menuPresmerovani' => $this->container->get('menu.presmerovani.editable')->setMenuRootName('menu_redirect'),
//                'menuVodorovne' => $this->container->get('menu.vodorovne.editable')->setMenuRootName('menu_horizontal'),
                ## var a
//                'menuSvisle' => $this->container->get('menu.svisle.editable')->setMenuRootName('root'),
                ## var b
//                'menuSvisle' => $this->container->get('menu.svisle.editable')->setMenuRootName('menu_vertical'),
//                'bloky' => $this->container->get('menu.bloky.editable')->setMenuRootName('blocks'), //menu.svisle.editable  //bloky
//                'kos' => $this->container->get('menu.kos')->setMenuRootName('trash'), //menu.svisle  //kos
//                ];

        } elseif ($this->isEditableArticle()) {
            $componets = [
                'menuPresmerovani' => $this->container->get('menu.presmerovani.editable')->setMenuRootName('menu_redirect'),
                'menuVodorovne' => $this->container->get('menu.vodorovne.editable')->setMenuRootName('menu_horizontal'),
                'menuSvisle' => $this->container->get('menu.svisle.editable')->setMenuRootName('menu_vertical'),
                'kos' => $this->container->get('menu.kos.editable')->setMenuRootName('trash'), //menu.svisle  //kos
                'bloky' => $this->container->get('menu.bloky.editable')->setMenuRootName('blocks'),
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

    private function getAuthoredLayoutComnponents() {
        if ($this->isEditableLayout()) {
            $componets = [
                    'aktuality' => $this->container->get('component.named.editable')->setComponentName('a1'),
                    'nejblizsiAkce' => $this->container->get('component.named.editable')->setComponentName('a2'),
                    'rychleOdkazy' => $this->container->get('component.named.editable')->setComponentName('a3'),
                    'razitko' => $this->container->get('component.named.editable')->setComponentName('a4'),
                    'socialniSite' => $this->container->get('component.named.editable')->setComponentName('a5'),
                    'mapa' => $this->container->get('component.named.editable')->setComponentName('a6'),
                    'logo' => $this->container->get('component.named.editable')->setComponentName('a7'),
                    'banner' => $this->container->get('component.named.editable')->setComponentName('a8'),
                ];
        } else {
            $componets = [
                    'aktuality' => $this->container->get('component.named')->setComponentName('a1'),
                    'nejblizsiAkce' => $this->container->get('component.named')->setComponentName('a2'),
                    'rychleOdkazy' => $this->container->get('component.named')->setComponentName('a3'),
                    'razitko' => $this->container->get('component.named')->setComponentName('a4'),
                    'socialniSite' => $this->container->get('component.named')->setComponentName('a5'),
                    'mapa' => $this->container->get('component.named')->setComponentName('a6'),
                    'logo' => $this->container->get('component.named')->setComponentName('a7'),
                    'banner' => $this->container->get('component.named')->setComponentName('a8'),
                ];
        }
        return $componets;
    }
    private function getXhrAuthoredLayoutComnponents() {
//        if ($this->isEditableLayout()) {
//            $componets = [
//                    'aktuality' => $this->container->get('component.named.editable')->setComponentName('a1'),
//                    'nejblizsiAkce' => $this->container->get('component.named.editable')->setComponentName('a2'),
//                    'rychleOdkazy' => $this->container->get('component.named.editable')->setComponentName('a3'),
//                    'razitko' => $this->container->get('component.named.editable')->setComponentName('a4'),
//                    'socialniSite' => $this->container->get('component.named.editable')->setComponentName('a5'),
//                    'mapa' => $this->container->get('component.named.editable')->setComponentName('a6'),
//                    'logo' => $this->container->get('component.named.editable')->setComponentName('a7'),
//                    'banner' => $this->container->get('component.named.editable')->setComponentName('a8'),
//                ];
//        } else {
            $componets = [
                    'aktuality' => $this->getComponentLoadScript('a1'),
                    'nejblizsiAkce' => $this->getComponentLoadScript('a2'),
                    'rychleOdkazy' => $this->getComponentLoadScript('a3'),
                    'razitko' => $this->getComponentLoadScript('a4'),
                    'socialniSite' => $this->getComponentLoadScript('a5'),
                    'mapa' => $this->getComponentLoadScript('a6'),
                    'logo' => $this->getComponentLoadScript('a7'),
                    'banner' => $this->getComponentLoadScript('a8'),
                ];
//        }
        return $componets;
    }

    private function getComponentLoadScript($componentName) {
        return $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::pageControler()['templates.loaderElement']))
                    ->setData([
                        'name' => $componentName,
                        'apiUri' => "component/v1/nameditem/$componentName/"
                        ]);
    }

    private function getStaticLoadScript($name) {
        return $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::pageControler()['templates.loaderElement']))
                    ->setData([
                        'name' => $name,
                        'apiUri' => "component/v1/static/$name"
                        ]);
    }
    #### komponenty, modal ######

    protected function getPoznamky() {
        if ($this->isEditableLayout() OR $this->isEditableArticle()) {
            return
                $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::pageControler()['templates.poznamky']))
                    ->setData([
                        'poznamka1'=>
                        '<pre>'. $this->prettyDump($this->statusPresentationRepo->get()->getLanguage(), true).'</pre>'
                        . '<pre>'. $this->prettyDump($this->statusSecurityRepo->get()->getUserActions(), true).'</pre>',
                        //'flashMessage' => $this->getFlashMessage(),
                        ]);
        }
    }

    protected function getModalLoginLogout() {
        $user = $this->statusSecurityRepo->get()->getUser();
        if (null != $user AND $user->getRole()) {   // libovolná role
            /** @var LogoutComponent $logoutComponent */
            $logoutComponent = $this->container->get(LogoutComponent::class);
            //$logoutComponent nepoužívá viewModel, používá template definovanou v kontejneru - zadávám data pro template
            $logoutComponent->setData(['userName' => $user->getUserName()]);
            return $logoutComponent;
        } else {
            /** @var LoginComponent $loginComponent */
            $loginComponent = $this->container->get(LoginComponent::class);
            //$loginComponent nepoužívá viewModel, používá template definovanou v kontejneru - zadávám data pro template
            $loginComponent->setData(["jmenoFieldName" => LoginLogoutController::JMENO_FIELD_NAME, "hesloFieldName" => LoginLogoutController::HESLO_FIELD_NAME]);
            return $loginComponent;
        }
    }

    protected function getModalUserAction() {
        $user = $this->statusSecurityRepo->get()->getUser();
        if (null != $user AND $user->getRole()) {   // libovolná role
            /** @var UserActionComponent $actionComponent */
            $actionComponent = $this->container->get(UserActionComponent::class);
            $actionComponent->setData(
                    [
                    'editArticle' => $this->isEditableArticle(),
                    'editLayout' => $this->isEditableLayout(),
                    'userName' => $user->getUserName()
                    ]);
            return $actionComponent;
        } else {

        }
    }

    private function prettyDump($var) {
//        return htmlspecialchars(var_export($var, true), ENT_QUOTES, 'UTF-8', true);
//        return htmlspecialchars(print_r($var, true), ENT_QUOTES, 'UTF-8', true);
        return $this->pp($var);
    }

    private function pp($arr){
        if (is_object($arr)) {
            $cls = get_class($arr);
            $arr = (array) $arr;
        } else {
            $cls = '';
        }
        $retStr = $cls ? "<p>$cls</p>" : "";
        $retStr .= '<ul>';
        if (is_array($arr)){
            foreach ($arr as $key=>$val){
                if (is_object($val)) $val = (array) $val;
                if (is_array($val)){
                    $retStr .= '<li>' . str_replace('\0', ':', $key) . ' = array(' . $this->pp($val) . ')</li>';
                }else{
                    $retStr .= '<li>' . str_replace($cls, "", $key) . ' = ' . ($val == '' ? '""' : $val) . '</li>';
                }
            }
        }
        $retStr .= '</ul>';
        return $retStr;
    }

    protected function getFlashComponent() {
        if ($this->isEditableLayout() OR $this->isEditableArticle()) {
            return $this->container->get(FlashComponent::class);
        }
    }
}
