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

use Status\Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};

// komponenty
use Component\View\{
    Generated\LanguageSelectComponent,
    Generated\SearchPhraseComponent,
    Generated\SearchResultComponent,
    Generated\ItemTypeSelectComponent,
    Status\LoginComponent, Status\RegisterComponent, Status\LogoutComponent, Status\UserActionComponent,
    Status\StatusBoardComponent, Status\ControlEditMenuComponent,

    Flash\FlashComponent
};

use Red\Model\Entity\MenuItemInterface;

####################
use Pes\View\ViewFactory;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;
use Pes\View\Renderer\ImplodeRenderer;

/**
 * Description of GetController
 *
 * @author pes2704
 */
abstract class LayoutControllerAbstract extends PresentationFrontControlerAbstract {

    private $viewFactory;

    protected $componentViews = [];


    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            ViewFactory $viewFactory) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->viewFactory = $viewFactory;
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

    ### response
    protected function createResponseWithItem(ServerRequestInterface $request, MenuItemInterface $menuItem = null) {
        if ($menuItem) {
            $this->setPresentationMenuItem($menuItem);
            $view = $this->createView($request, $this->getComponentViews($request, $menuItem));
            $response = $this->createResponseFromView($request, $view);
        } else {
            // neexistující stránka
            $response = $this->redirectSeeOther($request, ""); // SeeOther - ->home
        }
        return $response;
    }

######################################

    /**
     *
     * @return CompositeView
     */
    private function getCompositeView(ServerRequestInterface $request) {
        return $this->viewFactory->phpTemplateCompositeView(Configuration::layoutController()['layout'],
                [
                    'basePath' => $this->getBasePath($request),
                    'langCode' => $this->getPresentationLangCode(),
                    'title' => Configuration::layoutController()['title'],
                    'linksCommon' => Configuration::layoutController()['linksCommon'],
                    'linksSite' => Configuration::layoutController()['linksSite'],
                    'bodyContainerAttributes' => $this->getBodyContainerAttributes(),

                ]);
    }

    private function getBodyContainerAttributes() {
        if ($this->isAnyEditableMode()) {
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
                ['content' => $this->getMenuItemLoader($menuItem),
                'languageSelect' => $this->container->get(LanguageSelectComponent::class),
                'searchPhrase' => $this->container->get(SearchPhraseComponent::class),
                'modalLoginLogout' => $this->getLoginLogoutComponent(),
                'modalRegister' => $this->container->get(RegisterComponent::class),
                'modalUserAction' => $this->container->get(UserActionComponent::class),
                'linkEditorJs' => $this->getLinkEditorJsView($request),
                'linkEditorCss' => $this->getLinkEditorCssView($request),
                'poznamky' => $this->container->get(StatusBoardComponent::class),
                'flash' => $this->container->get(FlashComponent::class),
                'controlEditMenu' => $this->container->get(ControlEditMenuComponent::class),


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
            $menuItemId = $menuItem->getId();
            // prvek data ''loaderWrapperElementId' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
            $view = $this->container->get(View::class)
                        ->setData([
                            'loaderWrapperElementId' => "content_for_item_{$menuItemId}_with_type_{$menuItemType}",
                            'apiUri' => "web/v1/$menuItemType/$menuItemId"
                            ]);
        } else {
            $name = $this->getNameForStaticPage($menuItem);
            $view = $this->container->get(View::class)
                        ->setData([
                            'loaderWrapperElementId' => "content_for_item_{$name}_with_type_{$menuItemType}",
                            'apiUri' => "web/v1/$menuItemType/$name"
                            ]);
        }
        $view->setTemplate(new PhpTemplate(Configuration::pageController()['templates.loaderElement']));
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
    private function getLinkEditorJsView(ServerRequestInterface $request) {
        if ($this->isAnyEditableMode()) {
            ## document base path - stejná hodnota se musí použiít i v nastavení tinyMCE
            $basepath = $this->getBasePath($request);
            $tinyLanguage = Configuration::layoutController()['tinyLanguage'];
            $langCode =$this->statusPresentationRepo->get()->getLanguage()->getLangCode();
            $tinyToolsbarsLang = array_key_exists($langCode, $tinyLanguage) ? $tinyLanguage[$langCode] : Configuration::statusPresentationManager()['default_lang_code'];
            return
                $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::layoutController()['linksEditorJs']))
                    ->setData([
                        'tinyMCEConfig' => $this->container->get(View::class)
                                ->setTemplate(new InterpolateTemplate(Configuration::layoutController()['tiny_config']))
                                ->setData([
                                    // pro tiny_config.js
                                    'basePath' => $basepath,
                                    'paperTemplatesUri' =>  Configuration::layoutController()['paperTemplatesUri'],  // URI pro Template Controller
                                    'authorTemplatesPath' => Configuration::layoutController()['authorTemplatesPath'],
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

    private function getLinkEditorCssView(ServerRequestInterface $request) {
        if ($this->isAnyEditableMode()) {
            return $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::layoutController()['linkEditorCss']))
                    ->setData(
                            [
                            'linksCommon' => Configuration::layoutController()['linksCommon'],
                            ]
                            );
        }
    }

    ### pomocné private metody

    private function isAnyEditableMode() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        return $userActions->presentEditableArticle() OR $userActions->presentEditableLayout() OR $userActions->presentEditableMenu();
    }

}
