<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Web\Controller;

use Psr\Http\Message\ResponseInterface;

use Pes\Application\AppFactory;
use Pes\Application\UriInfoInterface;
use Controller\PresentationFrontControllerAbstract;
use Model\Repository\LanguageRepo;
use Pes\Http\Factory\ResponseFactory;
use Component\Controler\Generated\LanguageSelectComponent;
use Component\Controler\Generated\SearchPhraseComponent;
use Component\Controler\Generated\SearchResultComponent;
use Component\ViewModel\Authored\Paper\PresentedPaperViewModel;
use Component\Controler\Generated\ItemTypeSelectComponent;

####################
use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;
use Pes\View\Recorder\RecorderProvider;
use Pes\View\Recorder\VariablesUsageRecorder;
use Pes\View\Recorder\RecordsLogger;

use StatusModel\HierarchyStatusModelInterface;
use Psr\Container\ContainerInterface;

/**
 * Description of GetControler
 *
 * @author pes2704
 */
class GetController extends PresentationFrontControllerAbstract {

    /**
     * @var HierarchyStatusModelInterface
     */
    protected $statusModel;

    private $templatesLayout = [];
    /**
     *
     * @param HierarchyStatusModelInterface $statusModel
     * @param ContainerInterface $container
     */
    public function __construct(HierarchyStatusModelInterface $statusModel, ContainerInterface $container = NULL) {
        $this->statusModel = $statusModel;
        $this->container = $container;
        $this->initLayoutTemplatesVarsNew2();
    }

    private function initLayoutTemplatesVars() {
        $this->templatesLayout['layout'] = PROJECT_DIR.'/templates/layout.php';
        $this->templatesLayout['links'] = PROJECT_DIR.'/templates/layout/head/editableJsLinks.php';
        $this->templatesLayout['tiny_config'] = PROJECT_DIR.'/templates/layout/head/tiny_config.js';
    }
    private function initLayoutTemplatesVarsNew() {
        $this->templatesLayout['layout'] = PROJECT_DIR.'/templates/newlayout/layout.php';
        $this->templatesLayout['links'] = PROJECT_DIR.'/templates/newlayout/head/editableJsLinks.php';
        $this->templatesLayout['tiny_config'] = PROJECT_DIR.'/templates/newlayout/head/tiny_config.js';
    }
    private function initLayoutTemplatesVarsNew1() {
        $this->templatesLayout['layout'] = PROJECT_DIR.'/templates/newlayout_1/layout.php';
        $this->templatesLayout['links'] = PROJECT_DIR.'/templates/newlayout_1/head/editableJsLinks.php';
        $this->templatesLayout['tiny_config'] = PROJECT_DIR.'/templates/newlayout_1/head/tiny_config.js';
    }
    private function initLayoutTemplatesVarsNew2() {
        $this->templatesLayout['layout'] = PROJECT_DIR.'/templates/newlayout_2/layout.php';
        $this->templatesLayout['links'] = PROJECT_DIR.'/templates/newlayout_2/head/editableJsLinks.php';
        $this->templatesLayout['tiny_config'] = PROJECT_DIR.'/templates/newlayout_2/head/tiny_config.js';
    }
    private function initLayoutTemplatesVarsNew3() {
        $this->templatesLayout['layout'] = PROJECT_DIR.'/templates/newlayout_3/layout.php';
        $this->templatesLayout['links'] = PROJECT_DIR.'/templates/newlayout_3/head/editableJsLinks.php';
        $this->templatesLayout['tiny_config'] = PROJECT_DIR.'/templates/newlayout_3/head/tiny_config.js';
    }


    /**
     * Vrací pole dvojic jméno akce => role
     * @return array
     */
    public function getGrants() {
        return [
            'home' => '*',
            'item' => '*',
            'last' => '*',
            'searchResult' => '*',];
    }

    ### action metody ###############

    public function home() {
        if ($this->isPermittedMethod(__METHOD__)) {
            $this->statusModel->getPresentationStatus()->setItemUid('');  // status model nastaví default uid (jazyk zachová)
            return $this->createResponse($this->getContentView());
        }
    }

    public function item($uid) {
        if ($this->isPermittedMethod(__METHOD__)) {
            $this->statusModel->getPresentationStatus()->setItemUid($uid);
            return $this->createResponse($this->getContentView());
        }
    }

    public function last() {
        if ($this->isPermittedMethod(__METHOD__)) {
            return $this->createResponse($this->getContentView());
        }
    }

    public function searchResult() {
        if ($this->isPermittedMethod(__METHOD__)) {
            /** @var SearchResultComponent $component */
            $component = $this->container->get(SearchResultComponent::class);
            $key = $this->request->getAttribute('klic', '');
            $key = $this->request->getQueryParams()['klic'];
            $contentView = $component->setSearch($key);
            return $this->createResponse($contentView);
        }
    }

##### private methods ##############################################################

    private function isEditableLayout() {
        return $this->statusModel->getSecurityStatus()->getUser()->getUserStatus()->getEditLayout();
    }

    private function isEditableArticle() {
        return $this->statusModel->getSecurityStatus()->getUser()->getUserStatus()->getEditArticle();
    }

    /**
     * Vrací view objekt pro zobrazení centrálního obsahu v prostoru pro "content"
     * @return type
     */
    private function getContentView() {
        return $this->isEditableArticle() ? $this->container->get('article.headlined.editable') : $this->container->get('article.headlined');
    }

    private function getBasePath() {
        // nastavení uriInfo získaného z request (pro base path a přesměrování)
        /** @var UriInfoInterface $uriInfo */
        $uriInfo = $this->request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME);
        return $uriInfo->getSubdomainPath();
    }

    ### prezentace - response

    private function createResponse($contentView) {

        /* @var $user \Model\Entity\User */
//        $user = $this->getMwContainer()->get(\Model\Entity\User::class);
//        if (edit) {
//                        /* @var $handler Pes\Database\Handler\Handler */
//                        $handler = $mwContainer->get(\Pes\Database\Handler\Handler::class);
//                        $statement = $handler->query("SELECT stranka FROM activ_user WHERE user='".$user->getUser()."'");
//                        $statement->execute();
//                        if ($statement->rowCount() != 0) {
//                            $successUpdate = $handler->exec("UPDATE activ_user SET user = '".$user->getUser()."',stranka = 'null' WHERE user = '".$user->getUser()."'");
//                        } else {
//                            $successInsert = $handler->exec("INSERT INTO activ_user (user,stranka) VALUES ('".$user->getUser()."','null')");
//                        }
//        } else {
//                    $successDelete = $handler->exec("DELETE FROM activ_user WHERE user = '".$user->getUser()."' LIMIT 1");
//        }

        #### speed test ####
        $timer = new Timer();
        $timer->start();

        ## document base path
        $basepath = $this->getBasePath();
        ## langCode - pro html i pro hlavičky response
        $langCode = $this->statusModel->getPresentationStatus()->getLanguageCode();

        ## proměnné pro html
        $layoutContextData = $this->getTemplateVariables($basepath, $langCode);
        $contentData = ['content' => $contentView];
        $data = array_merge($layoutContextData, $contentData);
        $duration['Získání dat z modelu'] = $timer->interval();

        /* @var $view View */
        $view = $this->container->get(View::class);
        $view
            ->setTemplate(new PhpTemplate($this->templatesLayout['layout']))
            ->setData($data);
        $duration['Vytvoření view s template'] = $timer->interval();
        $html = $view->getString();   // vynutí renderování už zde
        $duration['Renderování template'] = $timer->interval();
        $this->container->get(RecordsLogger::class)
                ->logRecords($this->container->get(RecorderProvider::class));
        $duration['Zápis recordu do logu'] = $timer->interval();
        $duration['Celkem web middleware: '] = $timer->runtime();
        #### speed test výsledky jsou viditelné ve firebugu ####
        $html .= $this->createSpeedInfoHtml($duration);

        $response = (new ResponseFactory())->createResponse();

        ####  hlavičky  ####
        $response = $this->addHeaders($response, $langCode);

        ####  body  ####
        $size = $response->getBody()->write($html);
        $response->getBody()->rewind();

        return $response;
    }

    private function createSpeedInfoHtml($duration) {
        $testHtml[] = '<div style="display: none;">';
        foreach ($duration as $message => $interval) {
            $testHtml[] = "<p>$message:$interval</p>";
        }
        $testHtml[] = '</div>';
        return implode(PHP_EOL, $testHtml);
    }

    private function addHeaders(ResponseInterface $response, $langCode) {
        /** @var LanguageRepo $languageRepo */
        $languageRepo = $this->container->get(LanguageRepo::class);
        $language = $languageRepo->get($langCode);
        $response = $response->withHeader('Content-Language', $language->getLocale());
        if ($this->statusModel->getSecurityStatus()->getUser()->getUserStatus()->getEditArticle()) {
            $response = $response->withHeader('Cache-Control', 'no-cache');
        } else {
            $response = $response->withHeader('Cache-Control', 'public, max-age=180');
        }
        return $response;
    }

    private function getTemplateVariables($basepath, $langCode) {
        $context = [];
        $context = array_merge($context, $this->getVariables($basepath, $langCode));
        $context = array_merge($context, $this->getEditTools($basepath));
        $context = array_merge($context, $this->getGeneratedLayoutComponents());
        $context = array_merge($context, $this->getAuthoredLayoutComnponents());
        $context = array_merge($context, $this->getMenus());
        $context = array_merge($context, $this->getContentComponents());
        $context['modal'] = $this->getModal();

        return $context;
    }

    private function getVariables($basepath, $langCode) {

        return [
            'basePath' => $basepath,
            'langCode' => $langCode,
        ];
    }

    private function getEditTools($basepath) {
        if ($this->isEditableArticle() OR $this->isEditableLayout()) {
            $webPublicDir = \Middleware\Web\AppContext::getAppPublicDirectory();
            $commonPublicDir = \Middleware\Web\AppContext::getPublicDirectory();
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
                'poznamky' => $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate('templates/poznamky/poznamky.php'))
                    ->setData(['poznamka1'=> '<pre>'.print_r($this->statusModel->getPresentationStatus(), true).'</pre>']),
            ];
        } else {
            return [];
        }
    }

    private function getMenus() {
        if ($this->isEditableLayout()) {
            return [
                'menuPresmerovani' => $this->container->get('menu.presmerovani.editable')->setMenuRootName('l'),
                'menuVodorovne' => $this->container->get('menu.vodorovne.editable')->setMenuRootName('p'),
                ## var a
                'menuSvisle' => $this->container->get('menu.svisle.editable')->setMenuRootName('$'),
                ## var b
//                'menuSvisle' => $this->container->get('menu.svisle.editable')->setMenuRootName('s'),
//                'bloky' => $this->container->get('menu.bloky.editable')->setMenuRootName('block'), //menu.svisle.editable  //bloky
//                'kos' => $this->container->get('menu.kos')->setMenuRootName('trash'), //menu.svisle  //kos
            ];
        } else {
            if ($this->isEditableArticle()) {
                return [
                    'menuPresmerovani' => $this->container->get('menu.presmerovani.editable')->setMenuRootName('l'),
                    'menuVodorovne' => $this->container->get('menu.vodorovne.editable')->setMenuRootName('p'),
                    'menuSvisle' => $this->container->get('menu.svisle.editable')->setMenuRootName('s'),
                    'kos' => $this->container->get('menu.kos')->setMenuRootName('trash'), //menu.svisle  //kos
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
    
    private function getContentComponents() {
        if ($this->isEditableLayout() OR $this->isEditableArticle()) {
            return [
                'aktuality' => $this->container->get('component.headlined.editable')->setComponentName('a1'),
                'nejblizsiAkce' => $this->container->get('component.headlined.editable')->setComponentName('a2'),
                'rychleOdkazy' => $this->container->get('component.headlined.editable')->setComponentName('a3'),
                    'aktualita11' => $this->container->get('component.headlined')->setComponentName('a1'),
                ];
        } else {
            return [
                    'aktuality' => $this->container->get('component.headlined')->setComponentName('a1'),
                    'nejblizsiAkce' => $this->container->get('component.headlined')->setComponentName('a2'),
                    'rychleOdkazy' => $this->container->get('component.headlined')->setComponentName('a3'),
                    'aktualita11' => $this->container->get('component.headlined')->setComponentName('a1'),
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

    private function getModal() {
        if (null != $this->statusModel->getSecurityStatus()->getUser()->getRole()) {
            return $this->container->get(View::class)
                        ->setTemplate(new PhpTemplate(PROJECT_DIR.'/templates/modal/modal_user_action.php'))
                        ->setData([
                            'editArticle' => $this->statusModel->getSecurityStatus()->getUser()->getUserStatus()->getEditArticle(),
                            'editLayout' => $this->statusModel->getSecurityStatus()->getUser()->getUserStatus()->getEditLayout(),
                            'userName' => $this->statusModel->getSecurityStatus()->getUser()->getUserName()
                        ]);
        } else {
            return $this->container->get(View::class)
                        ->setTemplate(new PhpTemplate(PROJECT_DIR.'/templates/modal/modal_login.php'));
        }
    }
}
