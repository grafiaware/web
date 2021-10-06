<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Middleware\Page\Controller;

use Site\Configuration;

use FrontControler\PresentationFrontControlerAbstract;
use Psr\Http\Message\ServerRequestInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

// komponenty
use Component\View\Generated\LanguageSelectComponent;
use Component\View\Generated\SearchPhraseComponent;
use Component\View\Generated\SearchResultComponent;
use Component\View\Generated\ItemTypeSelectComponent;
use Component\View\Manage\LoginComponent;
use Component\View\Manage\RegisterComponent;
use Component\View\Manage\LogoutComponent;
use Component\View\Manage\UserActionComponent;
use Component\View\Manage\StatusBoardComponent;
use Component\View\Manage\ButtonEditMenuComponent;
use Component\View\Flash\FlashComponent;

use Red\Model\Entity\MenuItemInterface;

####################
use Pes\View\ViewFactory;
use Pes\View\View;
use Pes\View\ViewInterface;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;
use Pes\View\Renderer\ImplodeRenderer;

/**
 * Description of GetController
 *
 * @author pes2704
 */
abstract class LayoutControllerAbstract extends PresentationFrontControlerAbstract {

    protected $componentViews = [];

    ### response
    protected function createResponseWithItem(ServerRequestInterface $request, MenuItemInterface $menuItem = null) {
        if ($menuItem) {
            $this->setPresentationMenuItem($menuItem);
            $view = $this->createView($request, $this->getComponentViews($request, $menuItem));
            $response = $this->createResponseFromView($request, $view);
        } else {
            // neexistující stránka
            $response = $this->createResponseRedirectSeeOther($request, ""); // SeeOther - ->home
        }
        return $response;
    }

    ### composite view

    protected function createView(ServerRequestInterface $request, array $componentViews) {

        $layoutView = $this->getCompositeView($request);
        foreach ($componentViews as $name => $componentView) {
            if (isset($componentView)) {
                $layoutView->appendComponentView($componentView, $name);
            }
        }
        return $layoutView;
    }

######################################

    /**
     *
     * @return CompositeView
     */
    private function getCompositeView(ServerRequestInterface $request) {
        /** @var ViewInterface $view */
        $view = $this->container->get(View::class);
        $view->setTemplate(new PhpTemplate(Configuration::layoutController()['layout']));
        $view->setData(
                [
                    'basePath' => $this->getBasePath($request),
                    'langCode' => $this->getPresentationLangCode(),
                    'title' => Configuration::layoutController()['title'],
                    'linksCommon' => Configuration::layoutController()['linksCommon'],
                    'linksSite' => Configuration::layoutController()['linksSite'],
                    'bodyContainerAttributes' => $this->getBodyContainerAttributes(),
                    'isEditableMode' => $this->isEditableMode(),
                ]);
        return $view;
    }

    private function getBodyContainerAttributes() {
        if ($this->isEditableMode()) {
            return ["class" => "editable"];
        } else {
            return ["class" => ""];
        }
    }

    #### komponenty a komponentní views ######


    private function getComponentViews(ServerRequestInterface $request, MenuItemInterface $menuItem) {
        // POZOR! Nesmí se na stránce vyskytovat dva paper se stejným id v editovatelném režimu. TinyMCE vyrobí dvě hidden proměnné se stejným jménem
        // (odvozeným z id), ukládaný obsah editovatelné položky se neuloží - POST data obsahují prázdný řetězec a dojde potichu ke smazání obsahu v databázi.
        // Příklad: - bloky v editovatelném modu a současně editovatelné menu bloky - v menu bloky vybraný blok je zobrazen editovatelný duplicitně s blokem v layoutu
        //          - dva stené bloky v layoutu - mapa, kontakt v hlavičce i v patičce

        $views = array_merge(
                [
                'content' => $this->getMenuItemLoader($menuItem),
                'languageSelect' => $this->container->get(LanguageSelectComponent::class),
                'searchPhrase' => $this->container->get(SearchPhraseComponent::class),
                'modalLoginLogout' => $this->getLoginLogoutComponent(),
                'modalRegister' => $this->container->get(RegisterComponent::class),
                'modalUserAction' => $this->container->get(UserActionComponent::class),
                'poznamky' => $this->container->get(StatusBoardComponent::class),
                'flash' => $this->container->get(FlashComponent::class),
                'controlEditMenu' => $this->container->get(ButtonEditMenuComponent::class),

                'scriptsEditableMode' => $this->getScriptsEditableModeView($request),
                ],
                // full page
                $this->getAuthoredLayoutBlockLoaders(),
                // for debug
//                $this->getEmptyMenuComponents(),
                $this->getMenuComponents()
                );
        return $views;
    }

#
#### login nebo logout komponent #####################################################
#

    private function getLoginLogoutComponent() {
        $credentials = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (isset($credentials)) {
            return $this->container->get(LogoutComponent::class);
        } else {
            return $this->container->get(LoginComponent::class);
        }
    }

#
#### menu item loader #########################################################################
#

    /**
     * Vrací view objekt pro zobrazení centrálního obsahu v prostoru pro "content"
     * @return type
     */
    protected function getMenuItemLoader(MenuItemInterface $menuItem=null) {

        if (isset($menuItem)) {
            $content = $this->getContentLoadScript($menuItem);

        } else {
            // například neaktivní, neaktuální menu item
            $content = $this->container->get(View::class)->setRenderer(new ImplodeRenderer());
        }
        return $content;
    }

#
#### view s content loaderem #####################################################
#

    /**
     * Vrací view s šablonou obsahující skript pro načtení obsahu na základě reference menuItemId pomocí lazy load requestu a záměnu obsahu elementu v html stránky.
     * Parametr uri je id menuItem, aby nebylo třeba načítat paper nebo article zde v kontroleru.
     *
     * @param type $menuItem
     * @return type
     */
    protected function getContentLoadScript($menuItem) {
        $menuItemType = $menuItem->getTypeFk();
        if ($menuItemType!='static') {
            $id = $menuItem->getId();
        } else {
            $id = $this->getNameForStaticPage($menuItem);
        }
        // prvek data ''loaderWrapperElementId' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
        $view = $this->container->get(View::class)
                    ->setData([
                        'loaderWrapperElementId' => "content_for_item_{$id}_with_type_{$menuItemType}",
                        'apiUri' => "web/v1/$menuItemType/$id"
                        ]);
        $view->setTemplate(new PhpTemplate(Configuration::layoutController()['templates.loaderElement']));
        return $view;
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
        if ($this->isEditableMode()) {
            ## document base path - stejná hodnota se musí použiít i v nastavení tinyMCE
            $basepath = $this->getBasePath($request);
            $tinyLanguage = Configuration::layoutController()['tinyLanguage'];
            $langCode =$this->statusPresentationRepo->get()->getLanguage()->getLangCode();
            $tinyToolsbarsLang = array_key_exists($langCode, $tinyLanguage) ? $tinyLanguage[$langCode] : Configuration::presentationStatus()['default_lang_code'];
            return
                $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::layoutController()['scriptsEditableMode']))
                    ->setData([
                        'tinyMCEConfig' => $this->container->get(View::class)
                                ->setTemplate(new InterpolateTemplate(Configuration::layoutController()['tinyConfig']))
                                ->setData([
                                    // pro tiny_config.js
                                    'basePath' => $basepath,
                                    'toolbarsLang' => $tinyToolsbarsLang,
                                    // prvky pole contentCSS - tyto tři proměnné jsou prvky pole - pole je v tiny_config.js v proměnné contentCss
                                    'urlStylesCss' => Configuration::layoutController()['urlStylesCss'],
                                    'urlSemanticCss' => Configuration::layoutController()['urlSemanticCss'],
                                    'urlContentTemplatesCss' => Configuration::layoutController()['urlContentTemplatesCss']
                        ]),

                        'urlTinyMCE' => Configuration::layoutController()['urlTinyMCE'],
                        'urlJqueryTinyMCE' => Configuration::layoutController()['urlJqueryTinyMCE'],

                        'urlTinyInit' => Configuration::layoutController()['urlTinyInit'],
                        'editScript' => Configuration::layoutController()['urlEditScript'],
                    ]);
        }
    }

//    private function getLinkEditorCssView(ServerRequestInterface $request) {
//        return $this->container->get(View::class)
//                ->setTemplate(new PhpTemplate(Configuration::layoutController()['linkEditorCss']))
//                ->setData(
//                        [
//                        'linksCommon' => Configuration::layoutController()['linksCommon'],
//                        'isEditableMode' => $this->isEditableMode(),
//                        ]);
//    }

    ### pomocné private metody

    private function isEditableMode() {
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        return $userActions->presentEditableArticle() OR $userActions->presentEditableLayout() OR $userActions->presentEditableMenu();
    }

}
