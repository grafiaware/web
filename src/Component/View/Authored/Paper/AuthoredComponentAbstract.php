<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored\Paper;

use Component\View\CompositeComponentAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Pes\View\Template\PhpTemplate;

/**
 * Description of AuthoredComponentAbstract
 * Objekt je potomkem CompositeView.
 *
 * @author pes2704
 */
abstract class AuthoredComponentAbstract extends CompositeComponentAbstract implements AuthoredComponentInterface {

    /**
     * Přetěžuje view model Pes View, upřesňuje typ view modelu.
     * @var PaperViewModelInterface
     */
    protected $viewModel;

    /**
     * @var bool
     */
    protected $editable;

    /**
     * @var string
     */
    private $templatesPath = "undefined_paper_templates_path/";

    protected $templateGlobals = [];

    /**
     * Přetěžuje konstruktor CompositeComponentAbstract, upřesňuje typ parametru (view modelu).
     * @param PaperViewModelInterface $viewModel
     */
    public function __construct(PaperViewModelInterface $viewModel) {
        $this->viewModel = $viewModel;
    }

//    public function setEditable($editable) {
//        $this->editable = $editable;
//    }

    public function setPaperTemplatesPath($templatesPath) {
        $this->templatesPath = $templatesPath;
    }

    protected function getPaperTemplatePath($paperTemplateName) {
        return $this->templatesPath.$paperTemplateName."/template.php";
    }

    public function addTemplateGlobals($variableName, $rendererName) {
        $this->templateGlobals[$variableName] = $rendererName;
    }

    protected function resolveTemplate($templateName) {
        if (isset($templateName) AND $templateName) {
            $templatePath = $this->getPaperTemplatePath($templateName);
            try {
                $template = new PhpTemplate($templatePath);  // exception
                $sharedData = [];
                if (isset($this->rendererContainer)) {
                    foreach ($this->templateGlobals as $variableName => $rendererName) {
                        $sharedData[$variableName] = $this->rendererContainer->get($rendererName);
                    }
                }
                $template->setSharedData($sharedData);
            } catch (NoTemplateFileException $noTemplExc) {
                user_error("Neexistuje soubor šablony $templatePath", E_USER_WARNING);
            }
        }
        return $template ?? null;
    }
}
