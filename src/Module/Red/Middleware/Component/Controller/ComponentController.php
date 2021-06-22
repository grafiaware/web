<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Component\Controller;

use Site\Configuration;

use Psr\Http\Message\ServerRequestInterface;

// komponenty
use Component\View\{
    Flash\FlashComponent,
    Authored\Paper\PaperComponentInterface
};

// view pro kompilované static obsahy
use Pes\View\Renderer\PhpTemplateRenderer;
use Pes\View\Renderer\StringRenderer;

####################

use \GeneratorService\Paper\PaperService;

use Pes\Text\Message;

####################
//use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;

/**
 * Description of ComponentController
 *
 * @author pes2704
 */
class ComponentController extends XhrControllerAbstract {

    ### action metody ###############

    public function flash(ServerRequestInterface $request) {
        $view = $this->container->get(FlashComponent::class);
        return $this->createResponseFromView($request, $view);
    }

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
         $compiledContent=$this->getCompiledContent($request, $realName);
        return $this->createResponseFromString($request, $compiledContent);
    }

    public function content(ServerRequestInterface $request, $menuItemType, $menuItemId) {
        $view = $this->resolveMenuItemView($menuItemType, $menuItemId);
        return $this->createResponseFromView($request, $view);
    }

    ######################

    /**
     * Vrací view objekt pro zobrazení centrálního obsahu v prostoru pro "content"
     * @return type
     */
    private function resolveMenuItemView($menuItemType, $menuItemId) {
            $userActions = $this->statusSecurityRepo->get()->getUserActions();
            $isEditableContent = $userActions->isEditableArticle() OR $userActions->isEditableLayout();

            switch ($menuItemType) {
                case 'empty':
                    if ($isEditableContent) {
                        $view = $this->container->get(ItemTypeSelectComponent::class);
                    } else {
                        $view = $this->container->get(View::class)->setData(["Empty item."])->setRenderer(new ImplodeRenderer());
                    }
                    break;
                case 'generated':
                    $view = $view = $this->container->get(View::class)->setData( "No content for generated type.")->setRenderer(new StringRenderer());
                    break;
                case 'static':
                    if ($isEditableContent) {
                        $view = $this->getStaticEditableLoadScript($menuItem);
                    } else {
                        $view = $this->getStaticLoadScript($menuItem);
                    }
                    break;
                case 'paper':
                    /** @var PaperComponentInterface $view */
                    if ($isEditableContent) {
                        $view = $this->container->get('component.paper.editable');
                    } else {
                        $view = $this->container->get('component.paper');
                    }
                    $view->setItemId($menuItemId);

                    break;
                case 'template':
                    /** @var PaperComponentInterface $view */
                    if ($isEditableContent) {
                        $view = $this->container->get('component.template.editable');
                    } else {
                        $view = $this->container->get('component.template');
                    }
                    $view->setItemId($menuItemId);

                    break;
                case 'redirect':
                    $view = $view = $this->container->get(View::class)->setData( "No content for redirect type.")->setRenderer(new StringRenderer());
                    break;
                case 'root':
                        $view = $this->container->get(View::class)->setData( "root")->setRenderer(new StringRenderer());
                    break;
                case 'trash':
                        $view = $this->container->get(View::class)->setData( "trash")->setRenderer(new StringRenderer());
                    break;

                default:
                        $view = $this->container->get('component.presented');
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
        $templatePath = Configuration::componentController()['static'].$staticName;
        $templateFilename = $templatePath."/template.php";
        $compiledPath = Configuration::componentController()['compiled'];
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
        $file;
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
