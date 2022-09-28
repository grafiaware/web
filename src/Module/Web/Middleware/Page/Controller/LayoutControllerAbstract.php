<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Middleware\Page\Controller;

use Site\ConfigurationCache;

use FrontControler\PresentationFrontControlerAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\BlockRepo;

// komponenty
use Component\View\Generated\LanguageSelectComponent;
use Component\View\Generated\SearchPhraseComponent;
use Component\View\Manage\LoginLogoutComponent;
use Component\View\Manage\RegisterComponent;
use Component\View\Manage\UserActionComponent;
use Component\View\Manage\StatusBoardComponent;
use Component\View\Flash\FlashComponent;

use Red\Model\Entity\MenuItemInterface;

####################
use Pes\View\View;
use Pes\View\CompositeView;
use Pes\View\CompositeViewInterface;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;
use Pes\View\Renderer\ImplodeRenderer;

use Pes\Text\FriendlyUrl;

/**
 * Description of GetController
 *
 * @author pes2704
 */
abstract class LayoutControllerAbstract extends PresentationFrontControlerAbstract {

    protected $componentViews = [];

#
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
        /** @var BlockRepo $blockRepo */
        $blockRepo = $this->container->get(BlockRepo::class);
        $block = $blockRepo->get($name);

        // log!
//        if (!isset($block)) {
//            throw new \UnexpectedValueException("Undefined block defined as component with name '$name'.");
//        }
        return isset($block) ? $this->getMenuItem($block->getUidFk()) : null;  // není blok nebo není publikovaný&aktivní item
    }

#
#### response ################################
#
    /**
     * Metzoda pro pomtomkovský controler (page controler). Rozšiřuje funkčnost metod FrontControlerAbstract.
     * Vytvoří response pro položku v hierarchii - menu item. Pro neexistující menu item vytvoří response s přesměrováním na "home" stránku.
     *
     * @param ServerRequestInterface $request
     * @param MenuItemInterface $menuItem
     * @return ResponseInterface
     */
    protected function createResponseWithItem(ServerRequestInterface $request, MenuItemInterface $menuItem = null) {
        if ($menuItem) {
            $this->setPresentationMenuItem($menuItem);
            $view = $this->createView($request, $this->getComponentViews($request, $menuItem));
            $string = $view->getString();
//            $response = $this->createResponseFromView($request, $view);
            $response = $this->createResponseFromString($request, $string);
        } else {
            // neexistující stránka
            $response = $this->createResponseRedirectSeeOther($request, ""); // SeeOther - ->home
        }
        return $response;
    }

#
#### composite view
#
    /**
     * vytvoří kompozitní view z layout template a tomuto view nastaví defaultní data každé stránky.
     * Tomuto view přidá jako komponenty případné zadané komponentní views.
     *
     * @param ServerRequestInterface $request
     * @param array $componentViews
     * @return CompositeView
     */
    protected function createView(ServerRequestInterface $request, array $componentViews) {

        $layoutView = $this->getLayoutView($request);
        foreach ($componentViews as $name => $componentView) {
            if (isset($componentView)) {
                $layoutView->appendComponentView($componentView, $name);
            }
        }
        return $layoutView;
    }

######################################

    /**
     * View s tempate layout.php a data pro template
     * @return CompositeView
     */
    private function getLayoutView(ServerRequestInterface $request): CompositeViewInterface {
        /** @var CompositeViewInterface $view */
        $view = $this->container->get(CompositeView::class);
        $view->setTemplate(new PhpTemplate(ConfigurationCache::layoutController()['layout']));
        $view->setData(
                [
                    // v layout.php
                    'basePath' => $this->getBasePath($request),  // stejná metoda dává base path i do tinyConfig.js
                    'langCode' => $this->getPresentationLangCode(),
                    'title' => ConfigurationCache::layoutController()['title'],
                    // podmínění insert css souborů v head/css.php
                    'isEditableMode' => $this->isPartInEditableMode(),
                    // na mnoha místech - cesty k souborům zadané v konfiguraci
                    'linksCommon' => ConfigurationCache::layoutController()['linksCommon'],
                    'linksSite' => ConfigurationCache::layoutController()['linksSite'],
                    // atributy div v container.php
                    'bodyContainerAttributes' => $this->getBodyContainerAttributes(),
                ]);
        return $view;
    }

    private function getBodyContainerAttributes() {
        if ($this->isPartInEditableMode()) {
            return ["class" => "editable"];
        } else {
            return ["class" => ""];
        }
    }

    #### komponenty a komponentní views ######

    /**
     *
     * @param ServerRequestInterface $request
     * @param MenuItemInterface $menuItem
     * @return CompositeView[]
     */
    protected function getComponentViews(ServerRequestInterface $request, MenuItemInterface $menuItem) {
        // POZOR! Nesmí se na stránce vyskytovat dva paper se stejným id v editovatelném režimu. TinyMCE vyrobí dvě hidden proměnné se stejným jménem
        // (odvozeným z id), ukládaný obsah editovatelné položky se neuloží - POST data obsahují prázdný řetězec a dojde potichu ke smazání obsahu v databázi.
        // Příklad: - bloky v editovatelném modu a současně editovatelné menu bloky - v menu bloky vybraný blok je zobrazen editovatelný duplicitně s blokem v layoutu
        //          - dva stené bloky v layoutu - mapa, kontakt v hlavičce i v patičce

        $views = array_merge(
                [
                    'content' => $this->getContentLoadScript($menuItem),
                ],

                $this->getEditableModeViews($request),
                $this->getLoggedOnOffViews(),
                $this->getMenuComponents(),
                // for debug
//                $this->getEmptyMenuComponents(),
                // cascade
                $this->getAuthoredLayoutBlockLoaders(),
            );
        return $views;
    }

    private function getEditableModeViews($request) {
        if ($this->isPartInEditableMode()) {
            $views = [
                'scriptsEditableMode' => $this->getScriptsEditableModeView($request),
            ];
        } else {
           $views = [];
        }
        return $views;
    }

    private function getLoggedOnOffViews() {
        if($this->isUserLoggedIn()) {
            $views = [
                'modalLoginLogout' => $this->container->get(LoginLogoutComponent::class),
                'modalUserAction' => $this->container->get(UserActionComponent::class),
                'poznamky' => $this->container->get(StatusBoardComponent::class),
            ];
        } else {
            $views =  [
                'modalRegister' => $this->container->get(RegisterComponent::class),
                'modalLoginLogout' => $this->container->get(LoginLogoutComponent::class),
            ];
        }
        return $views;
    }

#
#### menu item loadery pro bloky layoutu #########################################################################
#

    /**
     * Vrací pole komponentů pro zobrazení obsahů v místech bloků layoutu. Pro bloky definované v konfiguraci vytvoří pole komponentních view obsahujících
     * skript pro načtení obsahu pomocí cascade.js v dalším requestu generovaném v prohlížeči.
     *
     * @return View[]
     */
    private function getAuthoredLayoutBlockLoaders() {
        $map = ConfigurationCache::layoutController()['layout_blocks'];
        $componets = [];

        // pro neexistující bloky nedělá nic
        foreach ($map as $variableName => $blockName) {
            $menuItem = $this->getMenuItemForBlock($blockName);
            if (isset($menuItem)) {
                $componets[$variableName] = $this->getContentLoadScript($menuItem);
            } else {
                $componets[$variableName] = $this->getUnknownContentView("Unknown block $blockName configured for layout variable $variableName.");
            }
        }
        return $componets;
    }

#
#### view s content loaderem #####################################################
#

    /**
     * Vrací view s šablonou obsahující skript pro načtení obsahu na základě typu menuItem a id menu item. Načtení probíhá pomocí cascade.js.
     * cascade.js odešle request a získá obsah a zámění původní obsah html elementu v layoutu.
     * Parametry uri v načítacím skriptu jsou typ menuItem a id menu item, aby nebylo třeba načítat data s obsahem (paper, article, multipage a další) zde v kontroleru.
     * Pro případ obsahu typu 'static' jsou jako prametry uri předány typ 'static' a jméno statické stránky, které je pak použito pro načtení statické šablony.
     *
     * @param type $menuItem
     * @return View
     */
    private function getContentLoadScript(MenuItemInterface $menuItem) {
        /** @var View $view */
        $view = $this->container->get(View::class);

        // prvek data 'loaderWrapperElementId' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
        $uniquid = uniqid();
        $menuItemType = $menuItem->getTypeFk();
        if (!isset($menuItemType)) {
            $menuItemType = 'empty';
        }
        switch ($menuItemType) {
            case 'static':
                $id = $this->getNameForStaticPage($menuItem);
                $dataRedApiUri = "red/v1/$menuItemType/$id";
                break;
            case 'eventcontent':
                $id = $this->getNameForStaticPage($menuItem);
                $dataRedApiUri = "events/v1/$menuItemType/$id";
                break;
            default:
                $id = $menuItem->getId();
                $dataRedApiUri = "red/v1/$menuItemType/$id";
                break;
        }
        $view->setData([
                        'loaderElementId' => "red_loaded_$uniquid",
                        'dataRedApiUri' => $dataRedApiUri,
                        'dataRedInfo' => "{$menuItemType}_for_item_{$id}",
                        'dataRedSelector' => $menuItem->getUidFk()
                        ]);
        $view->setTemplate(new PhpTemplate(ConfigurationCache::layoutController()['templates.loaderElement']));
        return $view;
    }

    private function getNameForStaticPage(MenuItemInterface $menuItem) {
        $menuItemPrettyUri = $menuItem->getPrettyuri();
        if (isset($menuItemPrettyUri) AND $menuItemPrettyUri AND strpos($menuItemPrettyUri, "folded:")===0) {      // EditItemController - line 93
            $name = str_replace('/', '_', str_replace("folded:", "", $menuItemPrettyUri));  // zahodí prefix a nahradí '/' za '_' - recipročně
        } else {
            $name = FriendlyUrl::friendlyUrlText($menuItem->getTitle());
        }
        return $name;
    }

    private function getUnknownContentView($message='') {
        /** @var View $view */
        $view = $this->container->get(View::class);
        $view->setTemplate(new PhpTemplate(ConfigurationCache::layoutController()['templates.unknownContent']))
                ->setData(['message'=>$message]);
        return $view;
    }

#
##### menu komponenty ##############################################################
#
    /**
     * Pole komponent generujících menu.
     *
     * @return View[]
     */
    private function getMenuComponents() {
        $components = [];
        foreach (ConfigurationCache::layoutController()['contextServiceMap'] as $contextName => $serviceName) {
            $components[$contextName] = $this->container->get($serviceName);
        }
        return $components;
    }
#
#### view s js a css pro editační mód #####################################################
#

    /**
     * Generuje html obsahující definice tagů <script> vkládaných do stránku pouze v editačním módu
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    private function getScriptsEditableModeView(ServerRequestInterface $request) {
        $tinyLanguage = ConfigurationCache::layoutController()['tinyLanguage'];
        $langCode =$this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $tinyToolsbarsLang = array_key_exists($langCode, $tinyLanguage) ? $tinyLanguage[$langCode] : ConfigurationCache::presentationStatus()['default_lang_code'];
        return
            $this->container->get(View::class)
                ->setTemplate(new PhpTemplate(ConfigurationCache::layoutController()['scriptsEditableMode']))
                ->setData([
                    'tinyMCEConfig' => $this->container->get(View::class)
                            ->setTemplate(new InterpolateTemplate(ConfigurationCache::layoutController()['tinyConfig']))
                            ->setData([
                                // pro tiny_config.js
                                'basePath' => $this->getBasePath($request),  // stejná metoda dáva base path i do layout.php
                                'toolbarsLang' => $tinyToolsbarsLang,
                                // prvky pole contentCSS - tyto tři proměnné jsou prvky pole - pole je v tiny_config.js v proměnné contentCss
                                'urlStylesCss' => ConfigurationCache::layoutController()['urlStylesCss'],
                                'urlSemanticCss' => ConfigurationCache::layoutController()['urlSemanticCss'],
                                'urlContentTemplatesCss' => ConfigurationCache::layoutController()['urlContentTemplatesCss'],
                                'urlMediaCss' => ConfigurationCache::layoutController()['urlMediaCss']
                    ]),

                    'urlTinyMCE' => ConfigurationCache::layoutController()['urlTinyMCE'],
                    'urlJqueryTinyMCE' => ConfigurationCache::layoutController()['urlJqueryTinyMCE'],

                    'urlTinyInit' => ConfigurationCache::layoutController()['urlTinyInit'],
                    'editScript' => ConfigurationCache::layoutController()['urlEditScript'],
                ]);
    }

    private function isPartInEditableMode() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        return isset($userActions) ? $userActions->presentAnyInEditableMode() : false;
    }

    private function isUserLoggedIn() {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        return (isset($loginAggregate) AND $loginAggregate->getLoginName()) ? true : false;
    }

}
