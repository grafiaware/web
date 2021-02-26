<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Xhr\Controller;

use Site\Configuration;

use Psr\Http\Message\ServerRequestInterface;
use Pes\Http\Request\RequestParams;

use Model\Entity\MenuItemInterface;
use Model\Entity\BlockAggregateMenuItemInterface;
use Model\Entity\PaperInterface;

// komponenty
use Component\View\{
    Generated\LanguageSelectComponent,
    Generated\SearchPhraseComponent,
    Generated\SearchResultComponent,
    Generated\ItemTypeSelectComponent,
    Flash\FlashComponent,
    Authored\Paper\PaperComponentInterface,
    Authored\Paper\NamedComponentInterface
};

// view pro kompilované static obsahy
use Pes\View\Renderer\PhpTemplateRenderer;
use Pes\View\Renderer\StringRenderer;

use \Middleware\Xhr\AppContext;

####################

use Model\Repository\{
    HierarchyAggregateRepo, MenuRootRepo, MenuItemRepo, BlockAggregateRepo, PaperRepo
};

use \GeneratorService\Paper\PaperService;

use \StatusManager\StatusPresentationManager;

use Pes\Text\Message;

####################
//use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;
//use Pes\View\Recorder\RecorderProvider;
//use Pes\View\Recorder\VariablesUsageRecorder;
//use Pes\View\Recorder\RecordsLogger;
use \Pes\View\ViewFactory;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
class ComponentControler extends XhrControlerAbstract {

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
        $compiledContent=$this->getCompiledContent($staticName);
        return $this->createResponseFromString($request, $compiledContent);
    }

    public function paper(ServerRequestInterface $request, $menuItemId) {
        /** @var PaperComponentInterface $component */
        $component = $this->container->get('component.paper');
        $component->setItemId($menuItemId);
        return $this->createResponseFromView($request, $component);
    }

    public function paperEditable(ServerRequestInterface $request, $menuItemId) {
        /** @var PaperComponentInterface $component */
        $component = $this->container->get('component.paper.editable');
        $component->setItemId($menuItemId);
        return $this->createResponseFromView($request, $component);
    }

    ######################

    /**
     * Vrací přeložený obsah statické šablony. Pokud přeložený obsah neexistuje, přeloží ho, t.j. renderuje statickou šablonu a uloží obsah do složky s přeloženými obsahy.
     *
     * @param type $staticName
     * @return string
     */
    private function getCompiledContent($staticName) {

        $compiledPath = Configuration::componentControler()['static']."__compiled/";
        $compiledFileName = $compiledPath.$staticName.".html";
        $templatePath = Configuration::componentControler()['static'].$staticName;
        $templateFilename = $templatePath."/template.php";
        if (is_readable($compiledFileName)) {
            $compiledFileTimestamp = filemtime($compiledFileName);  // Unix timestamp -> date ("d. F Y H:i:s.", $compiledFileTimestamp);
            $templateFolderTimestamp = $this->templateFolderModificationTime($templatePath);  // 7ms
            //(new \SplFileInfo($templatePath))->getMTime();
            $timeCompiled = date ("d. F Y H:i:s.", $compiledFileTimestamp);
            $timeTemlate = date ("d. F Y H:i:s.", $templateFolderTimestamp);
            $timeDiff = $compiledFileTimestamp - $templateFolderTimestamp;
            if ($templateFolderTimestamp < $compiledFileTimestamp ) {  // timestamp je s vteřinovou přesností
                $compiledContent = file_get_contents($compiledFileName);   // 100mikrosec
            } else {
                $compiledContent = $this->compileContent($templateFilename, $compiledFileName);   // 35ms
            }
        } else {
            $compiledContent = $this->compileContent($templateFilename, $compiledFileName);
        }
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

    private function compileContent($templateFilename, $compiledFileName) {
        if(!is_readable($templateFilename)) {
            $compiledContent = Message::t("Není čitený soubor statické stránky {file}.", ['file'=>$templateFilename]);
        } else {
            $view = new View();
            $view->setRenderer(new PhpTemplateRenderer());
            $view->setTemplate(new PhpTemplate($templateFilename));
            $compiledContent = $view->getString();
            file_put_contents($compiledFileName, $compiledContent);
        }
        return $compiledContent;
    }
}
