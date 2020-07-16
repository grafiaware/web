<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Web\Controller;

use Psr\Http\Message\ServerRequestInterface;

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
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;
//use Pes\View\Recorder\RecorderProvider;
//use Pes\View\Recorder\VariablesUsageRecorder;
//use Pes\View\Recorder\RecordsLogger;


/**
 * Description of GetControler
 *
 * @author pes2704
 */
class ComponentController extends LayoutControllerAbstract {

    const DEEAULT_HIERARCHY_ROOT_COMPONENT_NAME = 's';

    private $componentViews = [];

######################################
    private function initLayoutTemplatesVars() {
        $theme = 'old';

        switch ($theme) {
            case 'old':
                $this->templatesLayout['layout'] = PROJECT_DIR.'/templates/layout.php';
                $this->templatesLayout['links'] = PROJECT_DIR.'/templates/layout/head/editableJsLinks.php';
                $this->templatesLayout['tiny_config'] = PROJECT_DIR.'/templates/layout/head/tiny_config.js';
                break;
            case 'new':
                $this->templatesLayout['layout'] = PROJECT_DIR.'/templates/newlayout/layout.php';
                $this->templatesLayout['links'] = PROJECT_DIR.'/templates/newlayout/head/editableJsLinks.php';
                $this->templatesLayout['tiny_config'] = PROJECT_DIR.'/templates/newlayout/head/tiny_config.js';
                break;
            case 'new1':
                $this->templatesLayout['layout'] = PROJECT_DIR.'/templates/newlayout_1/layout.php';
                $this->templatesLayout['links'] = PROJECT_DIR.'/templates/newlayout_1/head/editableJsLinks.php';
                $this->templatesLayout['tiny_config'] = PROJECT_DIR.'/templates/newlayout_1/head/tiny_config.js';
                break;
            case 'new2':
                $this->templatesLayout['layout'] = PROJECT_DIR.'/templates/newlayout_2/layout.php';
                $this->templatesLayout['links'] = PROJECT_DIR.'/templates/newlayout_2/head/editableJsLinks.php';
                $this->templatesLayout['tiny_config'] = PROJECT_DIR.'/templates/newlayout_2/head/tiny_config.js';
                break;
            case 'new3':
                $this->templatesLayout['layout'] = PROJECT_DIR.'/templates/newlayout_3/layout.php';
                $this->templatesLayout['links'] = PROJECT_DIR.'/templates/newlayout_3/head/editableJsLinks.php';
                $this->templatesLayout['tiny_config'] = PROJECT_DIR.'/templates/newlayout_3/head/tiny_config.js';
                break;
            default:
                $this->templatesLayout['layout'] = PROJECT_DIR.'/templates/layout.php';
                $this->templatesLayout['links'] = PROJECT_DIR.'/templates/layout/head/editableJsLinks.php';
                $this->templatesLayout['tiny_config'] = PROJECT_DIR.'/templates/layout/head/tiny_config.js';
                break;
        }
    }

###############################################################################################################
    ### action metody ###############

    public function home(ServerRequestInterface $request) {
        $statusPresentation = $this->statusPresentationRepo->get();
        /** @var MenuRootRepo $menuRootRepo */
        $menuRootRepo = $this->container->get(MenuRootRepo::class);
        /** @var MenuItemRepo $menuItemRepo */
        $menuItemRepo = $this->container->get(MenuItemRepo::class);
        $uidFk = $menuRootRepo->get(StatusPresentationManager::DEEAULT_HIERARCHY_ROOT_COMPONENT_NAME)->getUidFk();
        $langCode = $statusPresentation->getLanguage()->getLangCode();
        $rootMenuItem = $menuItemRepo->get($langCode, $uidFk );    // kořen menu
        $statusPresentation->setMenuItem($rootMenuItem);

        $this->setContentComponent();
        return $this->createResponseFromView($request, $this->createView($request));
    }

    public function item(ServerRequestInterface $request, $langCode, $uid) {
        /** @var HierarchyNodeRepo $menuRepo */
        $menuRepo = $this->container->get(HierarchyNodeRepo::class);
        $menuNode = $menuRepo->get($langCode, $uid);
        if ($menuNode) {
            $this->statusPresentationRepo->get()->setMenuItem($menuNode->getMenuItem());
        } else {
            // neexistující stránka
            return $this->redirectSeeOther($request, ""); // SeeOther - ->home
        }

        $this->setContentComponent();
        return $this->createResponseFromView($request, $this->createView($request));
    }

    public function last(ServerRequestInterface $request) {
        $this->setContentComponent();
        return $this->createResponseFromView($request, $this->createView($request));
    }

    public function searchResult(ServerRequestInterface $request) {
        // TODO tady je nějaký zmatek
        /** @var SearchResultComponent $component */
        $component = $this->container->get(SearchResultComponent::class);
        $key = $request->getQueryParams()['klic'];
        $this->componentViews["content"] = $component->setSearch($key);
        return $this->createResponseFromView($request, $this->createView($request));
    }

##### private methods ##############################################################
#
    ### prezentace - view

    protected function createView(ServerRequestInterface $request) {
        #### speed test ####
//        $timer = new Timer();
//        $timer->start();
        $this->initLayoutTemplatesVars();
        $this->setComponentViews($request);

        $layoutView = $this->getLayoutView($request);
        foreach ($this->componentViews as $name => $componentView) {
            $layoutView->appendComponentView($componentView, $name);
        }

        return $layoutView;

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

    }

    private function createSpeedInfoHtml($duration) {
        $testHtml[] = '<div style="display: none;">';
        foreach ($duration as $message => $interval) {
            $testHtml[] = "<p>$message:$interval</p>";
        }
        $testHtml[] = '</div>';
        return implode(PHP_EOL, $testHtml);
    }

    protected function setComponentViews(ServerRequestInterface $request) {
        $this->componentViews = array_merge(
                $this->componentViews,
                $this->getEditTools($request),
                $this->getGeneratedLayoutComponents(),
                $this->getAuthoredLayoutComnponents(),
                $this->getEmptyMenuComponents(),
                $this->getMenuComponents(),
                $this->getLayoutComponents(),
                $this->getPoznamky()
                );
    }

    /**
     * Vrací view objekt pro zobrazení centrálního obsahu v prostoru pro "content"
     * @return type
     */
    private function setContentComponent() {
        $editable = $this->isEditableArticle();
        $menuItemType = $this->statusPresentationRepo->get()->getMenuItem()->getTypeFk();
            switch ($menuItemType) {
                case 'block':
                    if ($editable) {
                        $content = $this->container->get('article.block.editable');
                    } else {
                        $content = $this->container->get('article.block');
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
                    break;
            }
        $this->componentViews["content"] = $content;
    }

    private function getEditTools(ServerRequestInterface $request) {
        if ($this->isEditableArticle() OR $this->isEditableLayout()) {
            $webPublicDir = \Middleware\Web\AppContext::getAppPublicDirectory();
            $commonPublicDir = \Middleware\Web\AppContext::getPublicDirectory();
            ## document base path - stejná hodnota se musí použiít i v nastavení tinyMCE
            $basepath = $this->getBasePath($request);
            // Language packages tinyMce požívají krátké i dlouhé kódy (k=d odpovídá jménu souboru např cs.js) - proto mapování
            // pozn. - popisky šablon pro tiny jsou jen česky (TinyInit.js)
            $tinyLanguage = [
                'cs' => 'cs',
                'de' => 'de',
                'en' => 'en_US'
            ];
            $langCode =$this->statusPresentationRepo->get()->getLanguage()->getLangCode();
            $toolsbarsLang = array_key_exists($langCode, $tinyLanguage) ? $tinyLanguage[$langCode] : 'cs';
            return [
                'editableJsLinks' => $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate($this->templatesLayout['links']))
                    ->setData([
                        'tinyMCEConfig' => $this->container->get(View::class)
                            ->setTemplate(new InterpolateTemplate($this->templatesLayout['tiny_config']))
                            ->setData([
                                // pro tiny_config.js
                                'basePath' => $basepath,
                                'urlStylesCss' => $webPublicDir."grafia/css/styles.css",
                                'urlSemanticCss' => $webPublicDir."semantic/dist/semantic.min.css",
                                'urlZkouskaCss' => $webPublicDir."grafia/css/zkouska_less.css",
                                'templatesPath' => $commonPublicDir."tiny_templates/",
                                'toolbarsLang' => $toolsbarsLang
                            ]),
//                'urlTinyMCE' => $commonPublicDir.'tinymce/tinymce.min.js',
//                'urlJqueryTinyMCE' => $commonPublicDir.'tinymce/jquery.tinymce.min.js',

                'urlTinyMCE' => $commonPublicDir.'tinymce5_3_1\js\tinymce\tinymce.min.js',
                'urlJqueryTinyMCE' => $commonPublicDir.'tinymce5_3_1\js\tinymce\jquery.tinymce.min.js',

//                'urlTinyMCE' => $commonPublicDir.'tinymce5_3_1_dev\js\tinymce\tinymce.js',
//                'urlJqueryTinyMCE' => $commonPublicDir.'tinymce5_3_1_dev\js\tinymce\jquery.tinymce.min.js',

//    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/jquery.tinymce.min.js" referrerpolicy="origin"></script>
                'urlTinyInit' => $webPublicDir.'grafia/js/TinyInit.js',
                'editScript' => $webPublicDir . 'grafia/js/edit.js',
                'kalendarScript' => $webPublicDir . 'grafia/js/kalendar.js',
                    ]),

            ];
        } else {
            return [];
        }
    }

    private function getPoznamky() {
        if ($this->isEditableLayout() OR $this->isEditableArticle()) {
            $componets = [
                'poznamky' => $this->container->get(View::class)
                        ->setTemplate(new PhpTemplate('templates/poznamky/poznamky.php'))
                        ->setData([
                            'poznamka1'=>
                            '<pre>'. var_export($this->statusPresentationRepo->get()->getLanguage(), true).'</pre>'
                            . '<pre>'. var_export($this->statusSecurityRepo->get()->getUserActions(), true).'</pre>',
                            'flashMessage' => $this->getFlashMessage(),
                            ]),

            ];
        } else {
            $componets = [];
        }
        return $componets;
    }

    private function getFlashMessage() {
        $statusFlash = $this->statusFlashRepo->get();
        return $statusFlash ? $statusFlash->getFlash() ?? 'no flash' : 'no flash message';
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
    private function getGeneratedLayoutComponents() {
        return [
            'languageSelect' => $this->container->get(LanguageSelectComponent::class),
            'searchPhrase' => $this->container->get(SearchPhraseComponent::class),
        ];
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
