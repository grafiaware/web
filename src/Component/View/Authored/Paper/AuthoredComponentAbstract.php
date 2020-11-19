<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored\Paper;

use Pes\View\View;

use Component\View\CompositeComponentAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

/**
 * Description of AuthoredComponentAbstract
 * Objekt je potomkem CompositeView.
 *
 * @author pes2704
 */
abstract class AuthoredComponentAbstract extends CompositeComponentAbstract implements AuthoredComponentInterface {

    /**
     *
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
    protected $templatesPath;

    public function __construct(PaperViewModelInterface $viewModel) {
        $this->viewModel = $viewModel;
    }

    public function setEditable($editable) {
        $this->editable = $editable;
    }

    public function setTemplatesPath($templatesPath) {
        $this->templatesPath = $templatesPath;
    }


    /**
     * Pokud je paperAggregate a má nastavenu templateName, zde se komponentě nastaví template podle jména templateName.
     * Pokud není paperAggregate (je MenuItem s typem paper, ale ještě nevznikl Paper) nebo není nastaveno žádné jméno templateName, použije se standartně
     * (viz View) nastavený renderer nebo nastavený fallback renderer nebo interní fallback renderer.
     *
     * @param type $data
     * @return type
     */
    public function getString($data = null) {
        $paperAggregate = $this->viewModel->getPaperAggregate();
        if (isset($paperAggregate)) {
            if ($paperAggregate->isPersisted()) {
                $paperTemplateName = $paperAggregate->getTemplate();
                if (isset($paperTemplateName) AND $paperTemplateName) {
                    $templatePath = $this->templatesPath.$paperTemplateName."/template.php";
                    try {
                        $this->setTemplate(new PhpTemplate($templatePath));
                    } catch (NoTemplateFileException $noTemplExc) {
                        if ($paperTemplateName) {
                            user_error("Neexistuje soubor šablony $templatePath", E_USER_WARNING);
                        }
                        $this->setTemplate(null);
                    }
                }
            } else {

            }
        }
        return parent::getString($data);  // renderuj právě nastavenou šablonu neb použije renderer či fallback renderer

    }
}
