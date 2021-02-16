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

use Model\Repository\{
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
        return $this->viewFactory->phpTemplateCompositeView(Configuration::layoutControler()['layout'],
                [
                    'basePath' => $this->getBasePath($request),
                    'title' => Configuration::layoutControler()['title'],
                    'langCode' => $this->statusPresentationRepo->get()->getLanguage()->getLangCode(),
                    'linksCommon' => Configuration::layoutControler()['linksCommon'],
                    'linksSite' => Configuration::layoutControler()['linksSite'],
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
        if ($this->isEditableArticle() OR $this->isEditableLayout()) {
            return ["class" => "ui container editable"];
        } else {
            return ["class" => "ui container"];
        }
    }
    #### komponenty ######

    protected function getLoginLogoutComponent() {
        $credentials = $this->statusSecurityRepo->get()->getCredential();
        if (isset($credentials)) {
            /** @var LogoutComponent $logoutComponent */
            $logoutComponent = $this->container->get(LogoutComponent::class);
            //$logoutComponent nepoužívá viewModel, používá template definovanou v kontejneru - zadávám data pro template
            $logoutComponent->setData(['loginName' => $credentials->getLoginName()]);
            return $logoutComponent;
        } else {
            /** @var LoginComponent $loginComponent */
            $loginComponent = $this->container->get(LoginComponent::class);
            //$loginComponent nepoužívá viewModel, používá template definovanou v kontejneru - zadávám data pro template
            $loginComponent->setData([
                'fieldNameJmeno' => Configuration::loginLogoutControler()['fieldNameJmeno'],
                'fieldNameHeslo' => Configuration::loginLogoutControler()['fieldNameHeslo'],
                'fieldNameEmail' => Configuration::loginLogoutControler()['fieldNameEmail'],
                ]);
            return $loginComponent;
        }
    }

    protected function getRegisterComponent() {
        $user = $this->statusSecurityRepo->get()->getCredential();
        if (!isset($credentials)) {
            /** @var RegisterComponent $registerComponent */
            $registerComponent = $this->container->get(RegisterComponent::class);
            //$registerComponent nepoužívá viewModel, používá template definovanou v kontejneru - zadávám data pro template
            $registerComponent->setData([
                'fieldNameJmeno' => Configuration::loginLogoutControler()['fieldNameJmeno'],
                'fieldNameHeslo' => Configuration::loginLogoutControler()['fieldNameHeslo'],
                'fieldNameEmail' => Configuration::loginLogoutControler()['fieldNameEmail'],
                ]);
            return $registerComponent;
        }
    }

    protected function getUserActionComponent() {
        $user = $this->statusSecurityRepo->get()->getCredential();
        if (null != $user AND $user->getRole()) {   // libovolná role
            /** @var UserActionComponent $actionComponent */
            $actionComponent = $this->container->get(UserActionComponent::class);
            $actionComponent->setData(
                    [
                    'editArticle' => $this->isEditableArticle(),
                    'editLayout' => $this->isEditableLayout(),
                    'userName' => $user->getLoginName()
                    ]);
            return $actionComponent;
        } else {

        }
    }

    /**
     * Generuje html obsahující definice tagů <script> vkládaných do stránku pouze v editačním módu
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    private function getLinkEditorJsView(ServerRequestInterface $request) {
        if ($this->isEditableArticle() OR $this->isEditableLayout()) {
            ## document base path - stejná hodnota se musí použiít i v nastavení tinyMCE
            $basepath = $this->getBasePath($request);
            $tinyLanguage = Configuration::layoutControler()['tinyLanguage'];
            $langCode =$this->statusPresentationRepo->get()->getLanguage()->getLangCode();
            $tinyToolsbarsLang = array_key_exists($langCode, $tinyLanguage) ? $tinyLanguage[$langCode] : Configuration::statusPresentationManager()['default_lang_code'];
            return
                $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::layoutControler()['linksEditorJs']))
                    ->setData([
                        'tinyMCEConfig' => $this->container->get(View::class)
                                ->setTemplate(new InterpolateTemplate(Configuration::layoutControler()['tiny_config']))
                                ->setData([
//var tinyConfig = {
//    basePath: '/web/',
//    contentCss: ['public/site/common/css/old/styles.css', 'public/site/grafia/semantic-ui/semantic.min.css', '{{urlZkouskaCss}}'],
//    paper_templates_uri : 'public/site/grafia/templates/paper/',
//    content_templates_path : '{{contentTemplatesPath}}',
//    toolbarsLang: 'cs'
//};

                                    // pro tiny_config.js
                                    'basePath' => $basepath,
                                    'urlStylesCss' => Configuration::layoutControler()['urlStylesCss'],
                                    'urlSemanticCss' => Configuration::layoutControler()['urlSemanticCss'],
                                    'urlContentTemplatesCss' => Configuration::layoutControler()['urlContentTemplatesCss'],
                                    'paperTemplatesUri' =>  Configuration::layoutControler()['paperTemplatesUri'],  // URI pro Template controler
                                    'authorTemplatesPath' => Configuration::layoutControler()['authorTemplatesPath'],
                                    'toolbarsLang' => $tinyToolsbarsLang
                                ]),

                        'urlTinyMCE' => Configuration::layoutControler()['urlTinyMCE'],
                        'urlJqueryTinyMCE' => Configuration::layoutControler()['urlJqueryTinyMCE'],

                        'urlTinyInit' => Configuration::layoutControler()['urlTinyInit'],
                        'editScript' => Configuration::layoutControler()['urlEditScript'],
                    ]);
        }
    }

    private function getLinkEditorCssView(ServerRequestInterface $request) {
        if ($this->isEditableArticle() OR $this->isEditableLayout()) {
            return $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::layoutControler()['linkEditorCss']))
                    ->setData(
                            [
                            'linksCommon' => Configuration::layoutControler()['linksCommon'],
                            ]
                            );
        }
    }

    protected function getPoznamkyComponentView() {
        if ($this->isEditableLayout() OR $this->isEditableArticle()) {
            return
                $this->container->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::pageControler()['templates.poznamky']))
                    ->setData([
                        'poznamka1'=>
                        '<pre>'. $this->prettyDump($this->statusPresentationRepo->get()->getLanguage(), true).'</pre>'
                        . '<pre>'. $this->prettyDump($this->statusSecurityRepo->get()->getUserActions(), true).'</pre>',
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
        $retStr .= '<ul>';
        if (is_array($arr)){
            foreach ($arr as $key=>$val){
                if (is_object($val)) $val = (array) $val;
                if (is_array($val)){
                    $retStr .= '<li>' . str_replace('\0', ':', $key) . ' = array(' . $this->pp($val) . ')</li>';
                }else{
                    $retStr .= '<li>' . str_replace($cls, "", $key) . ' = ' . ($val == '' ? '""' : $val) . '</li>';
                }
            }
        }
        $retStr .= '</ul>';
        return $retStr;
    }

    protected function getFlashComponent() {
        if ($this->isEditableLayout() OR $this->isEditableArticle()) {
            return $this->container->get(FlashComponent::class);
        }
    }
}
