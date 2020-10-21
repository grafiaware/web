<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Web\Controller;

use Site\Configuration;

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
        return $this->viewFactory->phpTemplateCompositeView(Configuration::layoutControler()['layout'],
                [
                    'basePath' => $this->getBasePath($request),
                    'title' => Configuration::layoutControler()['title'],
                    'langCode' => $this->statusPresentationRepo->get()->getLanguage()->getLangCode(),
                    'webPublicDir' => Configuration::layoutControler()['webPublicDir'],
                    'webSitePublicDir' => Configuration::layoutControler()['webSitePublicDir'],
                    'modalLoginLogout' => $this->getModalLoginLogout(),
                    'modalUserAction' => $this->getModalUserAction(),
                    'bodyContainerAttributes' => $this->getBodyContainerAttributes(),
                    'linkEditJs' => $this->getEditJs($request),
                    'linkEditCss' => $this->getEditCss($request),
                    'poznamky' => $this->getPoznamky(),
                    'flash' => $this->getFlashComponent(),
                ]);
    }

    private function getBodyContainerAttributes() {
        if ($this->isEditableArticle() OR $this->isEditableLayout()) {
            return ["class" => "ui container editable"];
        } else {
            return ["class" => "ui container"];
        }
    }

    private function getEditJs(ServerRequestInterface $request) {
        if ($this->isEditableArticle() OR $this->isEditableLayout()) {
            $webPublicDir = \Middleware\Web\AppContext::getAppPublicDirectory();
            $commonPublicDir = \Middleware\Web\AppContext::getPublicDirectory();
            ## document base path - stejná hodnota se musí použiít i v nastavení tinyMCE
            $basepath = $this->getBasePath($request);
            $tinyLanguage = Configuration::layoutControler()['tinyLanguage'];
            $langCode =$this->statusPresentationRepo->get()->getLanguage()->getLangCode();
            $tinyToolsbarsLang = array_key_exists($langCode, $tinyLanguage) ? $tinyLanguage[$langCode] : Configuration::statusPresentationManager()['default_lang_code'];
            return
                $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::layoutControler()['linksJs']))
                    ->setData([
                        'tinyMCEConfig' => $this->container->get(View::class)
                                ->setTemplate(new InterpolateTemplate(Configuration::layoutControler()['tiny_config']))
                                ->setData([
                                    // pro tiny_config.js
                                    'basePath' => $basepath,
                                    'urlStylesCss' => Configuration::layoutControler()['urlStylesCss'],
                                    'urlSemanticCss' => Configuration::layoutControler()['urlSemanticCss'],
                                    'urlContentTemplatesCss' => Configuration::layoutControler()['urlContentTemplatesCss'],
                                    'paperTemplatesUri' =>  Configuration::layoutControler()['paperTemplatesUri'],  // URI pro Template controler
                                    'contentTemplatesPath' => Configuration::layoutControler()['contentTemplatesPath'],
                                    'toolbarsLang' => $tinyToolsbarsLang
                                ]),

                        'urlTinyMCE' => Configuration::layoutControler()['urlTinyMCE'],
                        'urlJqueryTinyMCE' => Configuration::layoutControler()['urlJqueryTinyMCE'],

                        'urlTinyInit' => Configuration::layoutControler()['urlTinyInit'],
                        'editScript' => Configuration::layoutControler()['editScript'],
                        'kalendarScript' => Configuration::layoutControler()['kalendarScript'],
                    ]);
        }
    }

    private function getEditCss(ServerRequestInterface $request) {
        if ($this->isEditableArticle() OR $this->isEditableLayout()) {
            return $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::layoutControler()['linksCss']))
                    ->setData(
                            [
                            'webPublicDir' => Configuration::layoutControler()['webPublicDir'],
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
                    ->setTemplate(new PhpTemplate(Configuration::layoutControler()['templates.poznamky']))
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
            return $this->container->get(FlashComponent::class);
        }
    }
}
