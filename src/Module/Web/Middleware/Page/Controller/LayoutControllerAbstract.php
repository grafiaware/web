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
            $actionComponents = ["content" => $this->resolveMenuItemView($menuItem)];
            $view = $this->createView($request, $this->getComponentViews($request, $actionComponents));
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
                    'title' => Configuration::layoutController()['title'],
                    'langCode' => $this->getPresentationLangCode(),
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

    #### komponenty a komponentbí views ######


    private function getComponentViews(ServerRequestInterface $request, array $actionComponents) {
        // POZOR! Nesmí se na stránce vyskytovat dva paper se stejným id v editovatelném režimu. TinyMCE vyrobí dvě hidden proměnné se stejným jménem
        // (odvozeným z id), ukládaný obsah editovatelné položky se neuloží - POST data obsahují prázdný řetězec a dojde potichu ke smazání obsahu v databázi.
        // Příklad: - bloky v editovatelném modu a současně editovatelné menu bloky - v menu bloky vybraný blok je zobrazen editovatelný duplicitně s blokem v layoutu
        //          - dva stené bloky v layoutu - mapa, kontakt v hlavičce i v patičce

        $views = array_merge(
                $actionComponents,
                $this->getLayoutComponents($request),
                // full page
                $this->getAuthoredLayoutComponents(),
                // for debug
//                $this->getEmptyMenuComponents(),
                $this->getMenuComponents()
                );
        return $views;
    }

    private function getLayoutComponents(ServerRequestInterface $request) {
        return [
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
        ];
    }

    private function getMenuComponents() {

        $userActions = $this->statusSecurityRepo->get()->getUserActions();

        $components = [];
        foreach (Configuration::pageController()['menu'] as $menuConf) {
            $this->configMenuComponent($menuConf, $components);
        }
        if ($userActions->presentEditableArticle()) {
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

    private function getLoginLogoutComponent() {
        $credentials = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (isset($credentials)) {
            return $this->container->get(LogoutComponent::class);
        } else {
            return $this->container->get(LoginComponent::class);
        }
    }

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
