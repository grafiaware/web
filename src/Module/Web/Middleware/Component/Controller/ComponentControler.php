<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Middleware\Component\Controller;

use Site\ConfigurationCache;
use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

// enum
use Red\Model\Enum\AuthoredTypeEnum;
//TODO: oprávnění pro routy
use Access\Enum\AllowedActionEnum;

// view model
use Component\ViewModel\Content\Authored\Paper\PaperViewModel;
use Component\ViewModel\Content\Authored\Article\ArticleViewModel;
use Component\ViewModel\Content\Authored\Multipage\MultipageViewModel;
use Component\ViewModel\Content\TypeSelect\ItemTypeSelectViewModel;

// komponenty
use Component\View\Content\TypeSelect\ItemTypeSelectComponent;
use Component\View\Content\Authored\Paper\PaperComponent;
use Component\View\Content\Authored\Paper\PaperComponentInterface;
use Component\View\Content\Authored\Article\ArticleComponent;
use Component\View\Content\Authored\Article\ArticleComponentInterface;
use Component\View\Content\Authored\Multipage\MultipageComponent;
use Component\View\Content\Authored\Multipage\MultipageComponentInterface;

// renderery
use Pes\View\Renderer\PhpTemplateRenderer;
use Pes\View\Renderer\StringRenderer;
use Pes\View\Renderer\ImplodeRenderer;
####################

use Pes\Text\Message;
use Pes\Text\Html;

####################
//use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;

/**
 * Description of ComponentController
 *
 * @author pes2704
 */
class ComponentControler extends FrontControlerAbstract {

    ### action metody ###############

    public function serviceComponent(ServerRequestInterface $request, $service) {
        if ($this->container->has($service)) {
            $view = $this->container->get($service);
        } else {
            $view = '';
        }
        return $this->createResponseFromView($request, $view);
    }

    public function static(ServerRequestInterface $request, $staticName) {
        $realName = str_replace('_', '/', $staticName);
        $compiledContent = $this->getCompiledContent($request, $realName);
        return $this->createResponseFromString($request, $compiledContent);
    }

    public function empty(ServerRequestInterface $request, $menuItemId) {
        if($this->isAllowed(AllowedActionEnum::GET)) {
            /** @var ItemTypeSelectViewModel $itemTypeSelectViewModel */
            $itemTypeSelectViewModel = $this->container->get(ItemTypeSelectViewModel::class);
            $itemTypeSelectViewModel->setMenuItemId($menuItemId);
            $view = $this->container->get(ItemTypeSelectComponent::class);
        } else {
            $view =  $this->getNonPermittedContentView(AllowedActionEnum::GET, AuthoredTypeEnum::PAPER);
        }
        return $this->createResponseFromView($request, $view);
    }

    public function paper(ServerRequestInterface $request, $menuItemId) {
        if($this->isAllowed(AllowedActionEnum::GET)) {
            /** @var PaperViewModel $paperViewModel */
            $paperViewModel = $this->container->get(PaperViewModel::class);
            $paperViewModel->setMenuItemId($menuItemId);
            /** @var PaperComponentInterface $view */
            $view = $this->container->get(PaperComponent::class);
        } else {
            $view =  $this->getNonPermittedContentView(AllowedActionEnum::GET, AuthoredTypeEnum::PAPER);
        }
        return $this->createResponseFromView($request, $view);
    }

    public function article(ServerRequestInterface $request, $menuItemId) {
        /** @var ArticleViewModel $viewModel */
        $viewModel = $this->container->get(ArticleViewModel::class);
        $viewModel->setMenuItemId($menuItemId);
        /** @var ArticleComponentInterface $view */
        $view = $this->container->get(ArticleComponent::class);
        return $this->createResponseFromView($request, $view);
    }

    public function multipage(ServerRequestInterface $request, $menuItemId) {
        /** @var MultipageViewModel $viewModel */
        $viewModel = $this->container->get(MultipageViewModel::class);
        $viewModel->setMenuItemId($menuItemId);
        /** @var MultipageComponentInterface $view */
        $view = $this->container->get(MultipageComponent::class);
        return $this->createResponseFromView($request, $view);
    }

    public function unknown(ServerRequestInterface $request) {
        $view = $this->container->get(View::class);
        $view->setData([Html::tag('div', ['style'=>'display: none;' ], 'Unknown content.')]);
        $view->setRenderer(new ImplodeRenderer());
        return $this->createResponseFromView($request, $view);
    }


    /**
     * Vrací view objekt pro zobrazení centrálního obsahu v prostoru pro "content"
     * @return type
     */
    private function resolveMenuItemView(ServerRequestInterface $request, $menuItemType, $menuItemId) {
            $userActions = $this->statusPresentationRepo->get()->getUserActions();
            $isEditableContent = $userActions->presentEditableContent() OR $userActions->presentEditableLayout();

            switch ($menuItemType) {
//                case 'empty':
//                        $view = $this->container->get(ItemTypeSelectComponent::class);
//                    break;
                case 'generated':
                    $view = $view = $this->container->get(View::class)->setData( ["No content for generated type."])->setRenderer(new ImplodeRenderer());
                    break;
//                case 'static':
//                    $view = $this->container->get('component.static_dosud_nevytvoreny');
//                    break;
//                case 'paper':
//                    /** @var PaperComponentInterface $view */
//                    $view = $this->container->get('component.paper');
//                    $view->setItemId($menuItemId);
//                    break;
//                case 'article':
//                    /** @var ContentComponentInterface $view */
//                    $view = $this->container->get('component.article');
//                    $view->setItemId($menuItemId);
//                    break;
                case 'redirect':
                    $view = $view = $this->container->get(View::class)->setData( ["No content for redirect type."])->setRenderer(new ImplodeRenderer());
                    break;
                case 'root':
                        $view = $this->container->get(View::class)->setData( ["root"])->setRenderer(new ImplodeRenderer());
                    break;
                case 'trash':
                        $view = $this->container->get(View::class)->setData( ["trash"])->setRenderer(new ImplodeRenderer());
                    break;

                default:
                    $view = $view = $this->container->get(View::class)->setData( ["Unknown component type."])->setRenderer(new ImplodeRenderer());
                    break;
            }

        return $view;
    }


    ###########################################

    /**
     * Vrací přeložený obsah statické šablony. Pokud přeložený obsah neexistuje, přeloží ho, t.j. renderuje statickou šablonu a uloží obsah do složky s přeloženými obsahy.
     *
     * @param type $staticName
     * @return string
     */
    private function getCompiledContent(ServerRequestInterface $request, $staticName) {
        $templatePath = ConfigurationCache::componentController()['static'].$staticName;
        $templateFilename = $templatePath."/template.php";
        $compiledPath = ConfigurationCache::componentController()['compiled'];
        $compiledFileName = $compiledPath.$staticName.".html";

//        if (is_readable($compiledFileName)) {
//            $compiledFileTimestamp = filemtime($compiledFileName);  // Unix timestamp -> date ("d. F Y H:i:s.", $compiledFileTimestamp);
//            $templateFolderTimestamp = $this->templateFolderModificationTime($templatePath);  // 7ms
//            //(new \SplFileInfo($templatePath))->getMTime();
//            $timeCompiled = date ("d. F Y H:i:s.", $compiledFileTimestamp);
//            $timeTemlate = date ("d. F Y H:i:s.", $templateFolderTimestamp);
//            $timeDiff = $compiledFileTimestamp - $templateFolderTimestamp;
//            if ($templateFolderTimestamp < $compiledFileTimestamp ) {  // timestamp je s vteřinovou přesností
//                $compiledContent = file_get_contents($compiledFileName);   // 100mikrosec
//            } else {
//                $compiledContent = $this->compileContent($templateFilename, $compiledFileName);   // 35ms
//            }
//        } else {
            $referrerPageUri = $request->getUri()->getPath();

            // kontejner do proměnných šalony
            $compiledContent = $this->compileContent($templateFilename, ['container'=> $this->container, 'referrerPageUri' => $referrerPageUri], $compiledFileName);   // ZAKOMENTOVÁNO UKLÁDÁNÍ
//        }
        return $compiledContent;
    }

    /**
     *
     * @param string $folderPath
     * @return int|bool UNIX time (v sekundách) nebo false pro prázdný adresář.
     */
    private function templateFolderModificationTime($folderPath) {
        $directory = new \RecursiveDirectoryIterator($folderPath);
        $iterator = new \RecursiveIteratorIterator($directory);
        $modTime = false;
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $fileModTime = $fileinfo->getMTime();
                if ($fileModTime > $modTime) {
                    $modTime = $fileModTime;
                }
            }
        }
        return $modTime;
    }

    private function compileContent($templateFilename, $context=[], $compiledFileName) {
        if(!is_readable($templateFilename)) {
            $compiledContent = Message::t("Není čitený soubor statické stránky {file}.", ['file'=>$templateFilename]);
        } else {
            $view = new View();
            $view->setRenderer(new PhpTemplateRenderer());
            $view->setTemplate(new PhpTemplate($templateFilename));
            if ($context) {
                $view->setData($context);
            }
            $compiledContent = $view->getString();
//            file_put_contents($compiledFileName, $compiledContent);   // !! VYPNUTO
        }
        return $compiledContent;
    }
}
