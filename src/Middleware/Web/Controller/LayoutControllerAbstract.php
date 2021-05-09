<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Web\Controller;

use Site\Configuration;

use FrontController\PresentationFrontControllerAbstract;
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
    Flash\FlashComponent
};

use Middleware\Login\Controller\LoginLogoutController;

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
abstract class LayoutControllerAbstract extends PresentationFrontControllerAbstract {

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

    ### prezentace - view

    protected function createView(ServerRequestInterface $request, array $componentViews) {

        $layoutView = $this->getCompositeView($request);
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
    private function getCompositeView(ServerRequestInterface $request) {
        return $this->viewFactory->phpTemplateCompositeView(Configuration::layoutController()['layout'],
                [
                    'basePath' => $this->getBasePath($request),
                    'title' => Configuration::layoutController()['title'],
                    'langCode' => $this->statusPresentationRepo->get()->getLanguage()->getLangCode(),
                    'linksCommon' => Configuration::layoutController()['linksCommon'],
                    'linksSite' => Configuration::layoutController()['linksSite'],
                    'bodyContainerAttributes' => $this->getBodyContainerAttributes(),
                    #### komponenty ######
                    'modalLoginLogout' => $this->getLoginLogoutComponent(),
                    'modalRegister' => $this->getRegisterComponent(),
                    'modalUserAction' => $this->getUserActionComponent(),
                    'linkEditorJs' => $this->getLinkEditorJsView($request),
                    'linkEditorCss' => $this->getLinkEditorCssView($request),
                    'poznamky' => $this->getPoznamkyComponentView(),
                    'flash' => $this->getFlashComponent(),
                ]);
    }

    private function getBodyContainerAttributes() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        $isEditableContent = $userActions->isEditableArticle() OR $userActions->isEditableLayout();
        if ($isEditableContent) {
            return ["class" => "ui container editable"];
        } else {
            return ["class" => "ui container"];
        }
    }
    #### komponenty ######

    protected function getLoginLogoutComponent() {
        $credentials = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (isset($credentials)) {
            /** @var LogoutComponent $logoutComponent */
            $logoutComponent = $this->container->get(LogoutComponent::class);
            //$logoutComponent nepoužívá viewModel, používá template a rendererContainer definované v kontejneru - zadávám jen data pro template
            $logoutComponent->setData(['loginName' => $credentials->getLoginName()]);
            return $logoutComponent;
        } else {
            /** @var LoginComponent $loginComponent */
            $loginComponent = $this->container->get(LoginComponent::class);
            //$loginComponent nepoužívá viewModel, používá template a rendererContainer definované v kontejneru - zadávám jen data pro template
//            $loginComponent->setData([
//                'fieldNameJmeno' => Configuration::loginLogoutController()['fieldNameJmeno'],
//                'fieldNameHeslo' => Configuration::loginLogoutController()['fieldNameHeslo'],
//                ]);
            return $loginComponent;
        }
    }

    protected function getRegisterComponent() {
        $credentials = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (!isset($credentials)) {
            /** @var RegisterComponent $registerComponent */
            $registerComponent = $this->container->get(RegisterComponent::class);
            //$registerComponent nepoužívá viewModel, používá template a rendererContainer definované v kontejneru - zadávám jen data pro template
//            $registerComponent->setData([
//                'fieldNameJmeno' => Configuration::loginLogoutController()['fieldNameJmeno'],
//                'fieldNameHeslo' => Configuration::loginLogoutController()['fieldNameHeslo'],
//                ]);
            return $registerComponent;
        }
    }

    protected function getUserActionComponent() {
        $loginAggregateCredentials = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (isset($loginAggregateCredentials)) {
            $role = $loginAggregateCredentials->getCredentials()->getRole();
            $permission = [
                'sup' => true,
                'editor' => true
            ];
            if (array_key_exists($role, $permission) AND $permission[$role]) {
                $userActions = $this->statusSecurityRepo->get()->getUserActions();
                /** @var UserActionComponent $actionComponent */
                $actionComponent = $this->container->get(UserActionComponent::class);
                $actionComponent->setData(
                        [
                        'editArticle' => $userActions->isEditableArticle(),
                        'editLayout' => $userActions->isEditableLayout(),
                        'editMenu' => $userActions->isEditableMenu(),
                        'userName' => $loginAggregateCredentials->getLoginName()
                        ]);
                return $actionComponent;
            }
        }
    }

    /**
     * Generuje html obsahující definice tagů <script> vkládaných do stránku pouze v editačním módu
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    private function getLinkEditorJsView(ServerRequestInterface $request) {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        $isEditableContent = $userActions->isEditableArticle() OR $userActions->isEditableLayout();
        if ($isEditableContent) {
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
                                    'urlStylesCss' => Configuration::layoutController()['urlStylesCss'],
                                    'urlSemanticCss' => Configuration::layoutController()['urlSemanticCss'],
                                    'urlContentTemplatesCss' => Configuration::layoutController()['urlContentTemplatesCss'],
                                    'paperTemplatesUri' =>  Configuration::layoutController()['paperTemplatesUri'],  // URI pro Template Controller
                                    'authorTemplatesPath' => Configuration::layoutController()['authorTemplatesPath'],
                                    'toolbarsLang' => $tinyToolsbarsLang
                                ]),

                        'urlTinyMCE' => Configuration::layoutController()['urlTinyMCE'],
                        'urlJqueryTinyMCE' => Configuration::layoutController()['urlJqueryTinyMCE'],

                        'urlTinyInit' => Configuration::layoutController()['urlTinyInit'],
                        'editScript' => Configuration::layoutController()['urlEditScript'],
                    ]);
        }
    }

    private function getLinkEditorCssView(ServerRequestInterface $request) {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        $isEditableContent = $userActions->isEditableArticle() OR $userActions->isEditableLayout();
        if ($isEditableContent) {
            return $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::layoutController()['linkEditorCss']))
                    ->setData(
                            [
                            'linksCommon' => Configuration::layoutController()['linksCommon'],
                            ]
                            );
        }
    }

    protected function getPoznamkyComponentView() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        $isEditableContent = $userActions->isEditableArticle() OR $userActions->isEditableLayout();
        if ($isEditableContent) {
            return
                $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::pageController()['templates.poznamky']))
                    ->setData([
                        'poznamka1'=>
                        $this->prettyDump($this->statusPresentationRepo->get()->getMenuItem(), true)
//                        .$this->prettyDump($this->statusPresentationRepo->get()->getLanguage(), true)
//                        . $this->prettyDump($this->statusSecurityRepo->get()->getUserActions(), true),
                        //'flashMessage' => $this->getFlashMessage(),
                        ]);
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
        $retStr .= '<ul style="margin:0px;">';
        if (is_array($arr)){
            foreach ($arr as $key=>$val){
                if (is_object($val)) $val = (array) $val;
                if (is_array($val)){
                    $retStr .= '<li style="padding:0px; background: #cce5ff">' . str_replace('\0', ':', $key) . ' = array(' . $this->pp($val) . ')</li>';
                }else{
                    $retStr .= '<li style="padding:0px; background: #ffe5cc">' . str_replace($cls, "", $key) . ' = ' . ($val == '' ? '""' : $val) . '</li>';
                }
            }
        }
        $retStr .= '</ul>';
        return $retStr;
    }

    protected function getFlashComponent() {
        return $this->container->get(FlashComponent::class);
    }
}
