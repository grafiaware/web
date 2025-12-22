<?php
namespace Template\Compiler;

use Psr\Http\Message\ServerRequestInterface;

use Site\ConfigurationCache;
use Pes\Text\Message;
use Pes\View\View;
use Pes\View\Renderer\PhpTemplateRenderer;
use Pes\View\Template\PhpTemplate;

use RecursiveDirectoryIterator;

/**
 * Description of TemplateCompiler
 *
 * @author pes2704
 */
class TemplateCompiler implements TemplateCompilerInterface {

    private $templateVariables = [];

    public function injectTemplateVars(array $templateVariables) {
        $this->templateVariables = $templateVariables;
    }

    /**
     * Vrací přeložený obsah statické šablony. Pokud přeložený obsah neexistuje, přeloží ho, t.j. renderuje statickou šablonu a uloží obsah do složky s přeloženými obsahy.
     *
     * @param string $templatePath
     * @return string
     */
    public function getCompiledContent(ServerRequestInterface $request, $templatePath): string {
        $templateFilename = $templatePath.self::TEMPLATE_FILE_NAME;
        $compiledPath = ConfigurationCache::componentControler()['compiled'];
        $compiledFileName = $compiledPath.$staticName.".html";
//TODO: Compiled - souboru s šablonou a souboru se zkompilovaným obsahem pomocí touch() nastavit stejný čas poslední změny -> pak pokud je šablona novější, překlad
// info: $datetime = DateTime::createFromFormat("n/d/Y h:i A T", $date); touch($filename, $datetime->getTimestamp()) ....
//
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

            // templateVariables do proměnných šalony, přednost mají proměnné nastavené v poli vlevo před +
            $compiledContent = $this->compileContent($templateFilename, ['referrerPageUri' => $referrerPageUri] + $this->templateVariables, $compiledFileName);   // ZAKOMENTOVÁNO UKLÁDÁNÍ
//        }
        return $compiledContent;
    }

    /**
     *
     * @param string $folderPath
     * @return int|bool UNIX time (v sekundách) nebo false pro prázdný adresář.
     */
    private function templateFolderModificationTime($folderPath) {
        $directory = new RecursiveDirectoryIterator($folderPath);
        $iterator = new RecursiveIteratorIterator($directory);
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
            $compiledContent = Message::t("Není čitelný soubor statické stránky {file}.", ['file'=>$templateFilename]);
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
