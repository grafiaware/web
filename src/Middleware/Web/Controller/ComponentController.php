<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Web\Controller;

use Pes\Database\Handler\Account;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Response\RedirectResponse;

// komponenty
use Component\View\{
    Generated\LanguageSelectComponent,
    Generated\SearchPhraseComponent,
    Generated\SearchResultComponent,
    Status\FlashComponent,
};

####################

use Model\Repository\{
    MenuRepo, StatusFlashRepo
};

####################
use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;
use Pes\View\Recorder\RecorderProvider;
use Pes\View\Recorder\VariablesUsageRecorder;
use Pes\View\Recorder\RecordsLogger;


/**
 * Description of GetControler
 *
 * @author pes2704
 */
class ComponentController extends LayoutControllerAbstract {
######################################
    private function initLayoutTemplatesVars() {
        $theme = 'new3';

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
        $view = $this->createView($request);

        return $this->createResponseFromView($request, $view);
    }

    public function item(ServerRequestInterface $request, $langCode, $uid) {
        /** @var MenuRepo $menuRepo */
        $menuRepo = $this->container->get(MenuRepo::class);
        $menuNode = $menuRepo->get($langCode, $uid);
        if ($menuNode) {
            $this->statusPresentationRepo->get()->setMenuItem($menuNode->getMenuItem());
        } else {
            // neexistující stránka
            return RedirectResponse::withRedirect(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/home/', $url, 303); // SeeOther
        }
        return $this->createResponseFromView($request, $this->createView($request));
    }

    public function last(ServerRequestInterface $request) {
        return $this->createResponseFromView($request, $this->createView($request));
    }

    public function searchResult(ServerRequestInterface $request) {
        // TODO tady je nějaký zmatek
        /** @var SearchResultComponent $component */
        $component = $this->container->get(SearchResultComponent::class);
        $key = $request->getAttribute('klic', '');
        $key = $request->getQueryParams()['klic'];
        $contentView = $component->setSearch($key);
        return $this->createResponseFromView($request, $this->createView($request));
    }

##### private methods ##############################################################
#
    ### prezentace - view

    protected function createView(ServerRequestInterface $request) {

        $this->initLayoutTemplatesVars();
        #### speed test ####
//        $timer = new Timer();
//        $timer->start();

        $layoutView = $this->getLayoutView($request);
        foreach ($this->getComponentViews($request) as $name => $componentView) {
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

    protected function getComponentViews(ServerRequestInterface $request) {
        $context = array_merge(
                $this->getEditTools($request),
                $this->getGeneratedLayoutComponents(),
                $this->getAuthoredLayoutComnponents(),
                $this->getEmptyMenuComponents(),
                $this->getMenuComponents(),
                $this->getLayoutComponents(),
                $this->getContentComponent(),
                $this->getPoznamky()
                );

        return $context;
    }

    /**
     * Vrací view objekt pro zobrazení centrálního obsahu v prostoru pro "content"
     * @return type
     */
    private function getContentComponent() {
        return ["content" => $this->isEditableArticle() ? $this->container->get('article.headlined.editable') : $this->container->get('article.headlined')];
    }

    private function getEditTools(ServerRequestInterface $request) {
        if ($this->isEditableArticle() OR $this->isEditableLayout()) {
            $webPublicDir = \Middleware\Web\AppContext::getAppPublicDirectory();
            $commonPublicDir = \Middleware\Web\AppContext::getPublicDirectory();
            ## document base path - stejná hodnota se musí použiít i v nastavení tinyMCE
            $basepath = $this->getBasePath($request);
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
                                'urlPrefixTemplatesTinyMce' => $webPublicDir."tiny_templates/",
                                'urlSemanticCss' => $webPublicDir."semantic/dist/semantic.min.css",
                                'urlZkouskaCss' => $webPublicDir."grafia/css/zkouska_less.css",
                            ]),
                        'urlTinyMCE' => $commonPublicDir.'tinymce/tinymce.min.js', // "https://cloud.tinymce.com/5/tinymce.min.js"
                        'urlTinyInit' => $webPublicDir.'grafia/js/TinyInit.js',
                        'editScript' => \Middleware\Web\AppContext::getAppPublicDirectory() . 'grafia/js/edit.js',
                        'kalendarScript' => \Middleware\Web\AppContext::getAppPublicDirectory() . 'grafia/js/kalendar.js',
                    ]),

            ];
        } else {
            return [];
        }
    }

    private function getPoznamky() {
        /** @var StatusFlashRepo $statusFlashRepo */
        $statusFlashRepo = $this->container->get(StatusFlashRepo::class);
        $statusFlash = $statusFlashRepo->get();
        return [
            'poznamky' => $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate('templates/poznamky/poznamky.php'))
                    ->setData([
                        'poznamka1'=>
                        '<pre>'. var_export($this->statusPresentationRepo->get()->getLanguage(), true).'</pre>'
                        . '<pre>'. var_export($this->statusSecurityRepo->get()->getUserActions(), true).'</pre>',
                        'flashMessage' => $statusFlash ? $statusFlash->getFlash() : 'no flash',
                        ]),

        ];
    }

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
                return [
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
                return [
                    'menuPresmerovani' => $this->container->get('menu.presmerovani')->setMenuRootName('l'),
                    'menuVodorovne' => $this->container->get('menu.vodorovne')->setMenuRootName('p'),
                    'menuSvisle' => $this->container->get('menu.svisle')->setMenuRootName('s'),
                    'bloky' => $this->container->get('menu.bloky.editable')->setMenuRootName('block'),
                ];
            }
        } else {
            if ($this->isEditableArticle()) {
                return [
                    'menuPresmerovani' => $this->container->get('menu.presmerovani.editable')->setMenuRootName('l'),
                    'menuVodorovne' => $this->container->get('menu.vodorovne.editable')->setMenuRootName('p'),
                    'menuSvisle' => $this->container->get('menu.svisle.editable')->setMenuRootName('s'),
                    'kos' => $this->container->get('menu.kos.editable')->setMenuRootName('trash'), //menu.svisle  //kos
                ];
            } else {
                return [
                    'menuPresmerovani' => $this->container->get('menu.presmerovani')->setMenuRootName('l'),
                    'menuVodorovne' => $this->container->get('menu.vodorovne')->setMenuRootName('p'),
                    'menuSvisle' => $this->container->get('menu.svisle')->setMenuRootName('s'),
                ];
            }
        }
    }

    private function getLayoutComponents() {
        if ($this->isEditableLayout() OR $this->isEditableArticle()) {
            return [
                    'aktuality' => $this->container->get('component.headlined.editable')->setComponentName('a1'),
                    'nejblizsiAkce' => $this->container->get('component.headlined.editable')->setComponentName('a2'),
                    'rychleOdkazy' => $this->container->get('component.headlined.editable')->setComponentName('a3'),
                ];
        } else {
            return [
                    'aktuality' => $this->container->get('component.headlined')->setComponentName('a1'),
                    'nejblizsiAkce' => $this->container->get('component.headlined')->setComponentName('a2'),
                    'rychleOdkazy' => $this->container->get('component.headlined')->setComponentName('a3'),
                ];
        }
    }
    private function getGeneratedLayoutComponents() {
        return [
            'languageSelect' => $this->container->get(LanguageSelectComponent::class),
            'searchPhrase' => $this->container->get(SearchPhraseComponent::class),
        ];
    }

    private function getAuthoredLayoutComnponents() {
        if ($this->isEditableLayout()) {
            return [
                    'razitko' => $this->container->get('component.block.editable')->setComponentName('a4'),
                    'socialniSite' => $this->container->get('component.block.editable')->setComponentName('a5'),
                    'mapa' => $this->container->get('component.block.editable')->setComponentName('a6'),
                    'logo' => $this->container->get('component.block.editable')->setComponentName('a7'),
                    'banner' => $this->container->get('component.block.editable')->setComponentName('a8'),
                ];
        } else {
            return [
                    'razitko' => $this->container->get('component.block')->setComponentName('a4'),
                    'socialniSite' => $this->container->get('component.block')->setComponentName('a5'),
                    'mapa' => $this->container->get('component.block')->setComponentName('a6'),
                    'logo' => $this->container->get('component.block')->setComponentName('a7'),
                    'banner' => $this->container->get('component.block')->setComponentName('a8'),
                ];
        }
    }

}
