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
                    'linksCommon' => Configuration::layoutControler()['linksCommon'],
                    'linksSite' => Configuration::layoutControler()['linksSite'],
                    'modalLoginLogout' => $this->getModalLoginLogout(),
                    'modalUserAction' => $this->getModalUserAction(),
                    'bodyContainerAttributes' => $this->getBodyContainerAttributes(),
                    'linkEditorJs' => $this->getEditorJs($request),
                    'linkEditorCss' => $this->getEditorCss($request),
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

    private function getEditorJs(ServerRequestInterface $request) {
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
                        'editScript' => Configuration::layoutControler()['editScript'],
                        'kalendarScript' => Configuration::layoutControler()['kalendarScript'],
                    ]);
        }
    }

    private function getEditorCss(ServerRequestInterface $request) {
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

}
