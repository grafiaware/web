<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Web\Controller;

use Controller\PresentationFrontControllerAbstract;
use Psr\Http\Message\ServerRequestInterface;

use Psr\Container\ContainerInterface;

use StatusManager\{
    StatusSecurityManagerInterface, StatusPresentationManagerInterface

};

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};

####################
use Pes\View\ViewFactory;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;

use Middleware\Login\Controller\LoginLogoutController;
use Component\View\{
    Status\LoginComponent, Status\LogoutComponent, Status\UserActionComponent,
    Flash\FlashComponent
};

/**
 * Description of GetControler
 *
 * @author pes2704
 */
abstract class LayoutControllerAbstract extends PresentationFrontControllerAbstract  implements LayoutControllerInterface  {

    private $viewFactory;

    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $componentViews = [];


    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            ViewFactory $viewFactory) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->viewFactory = $viewFactory;
    }

    public function injectContainer(ContainerInterface $componentContainer): LayoutControllerInterface {
        $this->container = $componentContainer;
        return $this;
    }

    protected function isEditableLayout() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        return $userActions ? $userActions->isEditableLayout() : false;
    }

    protected function isEditableArticle() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        return $userActions ? $userActions->isEditableArticle() : false;
    }

    ### prezentace - view

    protected function createView(ServerRequestInterface $request, array $componentViews) {
        #### speed test ####
//        $timer = new Timer();
//        $timer->start();
        $this->initLayoutTemplatesVars();

        $layoutView = $this->getLayoutView($request);
        foreach ($componentViews as $name => $componentView) {
            $layoutView->appendComponentView($componentView, $name);
        }
        return $layoutView;
    }

######################################

    /**
     *
     * @return CompositeView
     */
    private function getLayoutView(ServerRequestInterface $request) {
        return $this->viewFactory->phpTemplateCompositeView($this->templatesLayout['layout'],
                [
                    'basePath' => $this->getBasePath($request),
                    'title' => \Middleware\Web\AppContext::getWebTitle(),
                    'langCode' => $this->statusPresentationRepo->get()->getLanguage()->getLangCode(),
                    'webPublicDir' => \Middleware\Web\AppContext::getAppPublicDirectory(),
                    'webSitePublicDir' =>\Middleware\Web\AppContext::getAppSitePublicDirectory(),
                    'modalLoginLogout' => $this->getModalLoginLogout(),
                    'modalUserAction' => $this->getModalUserAction(),
                    'bodyContainerAttributes' => $this->getBodyContainerAttributes(),
                    'editableJsLinks' => $this->getEditTools($request),
                    'editableCssLinks' => $this->getEditCss($request),
                    'poznamky' => $this->getPoznamky(),
                    'flash' => $this->getFlashComponent(),
                ]);
    }

    private function initLayoutTemplatesVars() {
        $theme = 'old';

        switch ($theme) {
            case 'old':
                $this->templatesLayout['layout'] = PROJECT_PATH.'templates/layout/layout.php';
                $this->templatesLayout['linksJs'] = PROJECT_PATH.'templates/layout/head/editableJsLinks.php';
                $this->templatesLayout['linksCss'] = PROJECT_PATH.'templates/layout/head/editableCssLinks.php';
                $this->templatesLayout['tiny_config'] = PROJECT_PATH.'templates/layout/head/tiny_config.js';
                break;
            case 'xhr':
                $this->templatesLayout['layout'] = PROJECT_PATH.'templates/layoutXhr/layout.php';
                $this->templatesLayout['linksJs'] = PROJECT_PATH.'templates/layoutXhr/head/editableJsLinks.php';
                $this->templatesLayout['linksCss'] = PROJECT_PATH.'templates/layout/head/editableCssLinks.php';
                $this->templatesLayout['tiny_config'] = PROJECT_PATH.'templates/layoutXhr/head/tiny_config.js';
                break;
            case 'new':
                $this->templatesLayout['layout'] = PROJECT_PATH.'templates/newlayout/layout.php';
                $this->templatesLayout['linksJs'] = PROJECT_PATH.'templates/newlayout/head/editableJsLinks.php';
                $this->templatesLayout['linksCss'] = PROJECT_PATH.'templates/layout/head/editableCssLinks.php';
                $this->templatesLayout['tiny_config'] = PROJECT_PATH.'templates/newlayout/head/tiny_config.js';
                break;
            case 'new1':
                $this->templatesLayout['layout'] = PROJECT_PATH.'templates/newlayout_1/layout.php';
                $this->templatesLayout['linksJs'] = PROJECT_PATH.'templates/newlayout_1/head/editableJsLinks.php';
                $this->templatesLayout['linksCss'] = PROJECT_PATH.'templates/layout/head/editableCssLinks.php';
                $this->templatesLayout['tiny_config'] = PROJECT_PATH.'templates/newlayout_1/head/tiny_config.js';
                break;
            case 'new2':
                $this->templatesLayout['layout'] = PROJECT_PATH.'templates/newlayout_2/layout.php';
                $this->templatesLayout['linksJs'] = PROJECT_PATH.'templates/newlayout_2/head/editableJsLinks.php';
                $this->templatesLayout['linksCss'] = PROJECT_PATH.'templates/layout/head/editableCssLinks.php';
                $this->templatesLayout['tiny_config'] = PROJECT_PATH.'templates/newlayout_2/head/tiny_config.js';
                break;
            case 'new3':
                $this->templatesLayout['layout'] = PROJECT_PATH.'templates/newlayout_3/layout.php';
                $this->templatesLayout['linksJs'] = PROJECT_PATH.'templates/newlayout_3/head/editableJsLinks.php';
                $this->templatesLayout['linksCss'] = PROJECT_PATH.'templates/layout/head/editableCssLinks.php';
                $this->templatesLayout['tiny_config'] = PROJECT_PATH.'templates/newlayout_3/head/tiny_config.js';
                break;
            default:
                $this->templatesLayout['layout'] = PROJECT_PATH.'templates/layout/layout.php';
                $this->templatesLayout['linksJs'] = PROJECT_PATH.'templates/layout/head/editableJsLinks.php';
                $this->templatesLayout['linksCss'] = PROJECT_PATH.'templates/layout/head/editableCssLinks.php';
                $this->templatesLayout['tiny_config'] = PROJECT_PATH.'templates/layout/head/tiny_config.js';
                break;
        }
    }

    private function getBodyContainerAttributes() {
        if ($this->isEditableArticle() OR $this->isEditableLayout()) {
            return ["class" => "ui container editable"];
        } else {
            return ["class" => "ui container"];
        }
    }

    private function getEditTools(ServerRequestInterface $request) {
        if ($this->isEditableArticle() OR $this->isEditableLayout()) {
            $webPublicDir = \Middleware\Web\AppContext::getAppPublicDirectory();
            $webSitePublicDir = \Middleware\Web\AppContext::getAppSitePublicDirectory();
            $commonPublicDir = \Middleware\Web\AppContext::getPublicDirectory();
            $tinyPublicDir = \Middleware\Web\AppContext::getTinyPublicDirectory();
            ## document base path - stejná hodnota se musí použiít i v nastavení tinyMCE
            $basepath = $this->getBasePath($request);
            // Language packages tinyMce požívají krátké i dlouhé kódy, kód odpovídá jménu souboru např cs.js nebo en_US.js - proto mapování
            // pozn. - popisky šablon pro tiny jsou jen česky (TinyInit.js)
            $tinyLanguage = [
                'cs' => 'cs',
                'de' => 'de',
                'en' => 'en_US'
            ];
            $langCode =$this->statusPresentationRepo->get()->getLanguage()->getLangCode();
            $tinyToolsbarsLang = array_key_exists($langCode, $tinyLanguage) ? $tinyLanguage[$langCode] : 'cs';
            return
                $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate($this->templatesLayout['linksJs']))
                    ->setData([
                        'tinyMCEConfig' => $this->container->get(View::class)
                                ->setTemplate(new InterpolateTemplate($this->templatesLayout['tiny_config']))
                                ->setData([
                                    // pro tiny_config.js
                                    'basePath' => $basepath,
                                    'urlStylesCss' => $webPublicDir."styles/old/styles.css",
                                    'urlSemanticCss' => $webPublicDir."semantic/dist/semantic.min.css",
                                    'urlContentTemplatesCss' => $webPublicDir."templates/author/template.css",
                                    'paperTemplatesUri' =>  $webPublicDir."templates/paper/",  // URI pro Template controler
                                    'contentTemplatesPath' => $webPublicDir."templates/author/",
                                    'toolbarsLang' => $tinyToolsbarsLang
                                ]),

                        'urlTinyMCE' => $commonPublicDir.'tinymce5_3_1\js\tinymce\tinymce.min.js',
                        'urlJqueryTinyMCE' => $commonPublicDir.'tinymce5_3_1\js\tinymce\jquery.tinymce.min.js',
//                        'urlTinyMCE' => $commonPublicDir.'tinymce5_4_0\js\tinymce\tinymce.min.js',
//                        'urlJqueryTinyMCE' => $commonPublicDir.'tinymce5_4_0\js\tinymce\jquery.tinymce.min.js',

//    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/jquery.tinymce.min.js" referrerpolicy="origin"></script>
                        'urlTinyInit' => $webPublicDir.'js/TinyInit.js',
                        'editScript' => $webPublicDir . 'js/edit.js',
                        'kalendarScript' => $webPublicDir . 'js/kalendar.js',
                    ]);
        }
    }

    private function getEditCss(ServerRequestInterface $request) {
        if ($this->isEditableArticle() OR $this->isEditableLayout()) {
            return                 $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate($this->templatesLayout['linksCss']))
                    ->setData(
                            [
                            'webPublicDir' => \Middleware\Web\AppContext::getAppPublicDirectory(),
                            ]
                            );
        }
    }
    private function getModalLoginLogout() {
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

    private function getModalUserAction() {
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

    private function getPoznamky() {
        if ($this->isEditableLayout() OR $this->isEditableArticle()) {
            return
                $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate('templates/poznamky/poznamky.php'))
                    ->setData([
                        'poznamka1'=>
                        '<pre>'. $this->prettyDump($this->statusPresentationRepo->get()->getLanguage(), true).'</pre>'
                        . '<pre>'. $this->prettyDump($this->statusSecurityRepo->get()->getUserActions(), true).'</pre>',
                        //'flashMessage' => $this->getFlashMessage(),
                        ]);
        }
    }

    private function prettyDump($var) {
        return htmlspecialchars(var_export($var, true), ENT_QUOTES, 'UTF-8', true);
//        return htmlspecialchars(print_r($var, true), ENT_QUOTES, 'UTF-8', true);
//        return $this->pp($var);
    }

    private function pp($arr){
        if (is_object($arr))
            $arr = (array) $arr;
        $retStr = '<ul>';
        if (is_array($arr)){
            foreach ($arr as $key=>$val){
                if (is_object($val))
                    $val = (array) $val;
                if (is_array($val)){
                    $retStr .= '<li>' . str_replace('\0', ':', $key) . ' => array(' . $this->pp($val) . ')</li>';
                }else{
                    $retStr .= '<li>' . $key . ' => ' . ($val == '' ? '""' : $val) . '</li>';
                }
            }
        }
        $retStr .= '</ul>';
        return $retStr;
    }

    private function getFlashComponent() {
        if ($this->isEditableLayout() OR $this->isEditableArticle()) {
//            $statusFlash = $this->statusFlashRepo->get();
//            return
//                $this->container->get(View::class)
//                    ->setTemplate(new PhpTemplate('templates/poznamky/flashMessage.php'))
//                    ->setData([
//                        'flashMessage' => $statusFlash ? $statusFlash->getMessage() ?? 'no flash' : 'no flash message'
//                        ]);
            return $this->container->get(FlashComponent::class);
        }
    }
}
