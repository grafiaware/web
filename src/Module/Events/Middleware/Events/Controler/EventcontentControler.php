<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;
use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

// renderery
use Pes\View\Renderer\PhpTemplateRenderer;

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
class EventcontentControler extends FrontControlerAbstract {

    ### action metody ###############

    public function static(ServerRequestInterface $request, $staticName) {
        $realName = str_replace('_', '/', $staticName);
        $compiledContent = $this->getCompiledContent($request, $realName);
        return $this->createResponseFromString($request, $compiledContent);
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
