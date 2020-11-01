<?php
namespace Component\View\Authored\Paper;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\View\Authored\AuthoredComponentInterface;

use Pes\View\Template\Exception\NoTemplateFileException;
use Pes\View\Template\PhpTemplate;

use Component\ViewModel\Authored\Paper\PresentedPaperViewModelInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PresentedItemComponent
 *
 * @author pes2704
 */
class PresentedPaperComponent extends AuthoredComponentAbstract implements AuthoredComponentInterface {

    /**
     * @var PresentedPaperViewModelInterface
     */
    protected $viewModel;

    /**
     *
     * @param PresentedPaperViewModelInterface $viewModel
     */
    public function __construct(PresentedPaperViewModelInterface $viewModel) {
        $this->viewModel = $viewModel;
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
            $paperTemplateName = $paperAggregate->getTemplate();
            if (isset($paperTemplateName) AND $paperTemplateName) {
                $templatePath = PROJECT_PATH."public/web/templates/paper/".$paperTemplateName."/template.php";
                try {
                    $this->setTemplate(new PhpTemplate($templatePath));
                } catch (NoTemplateFileException $noTemplExc) {
                    if ($paperTemplateName) {
                        user_error("Neexistuje soubor šablony $templatePath", E_USER_WARNING);
                    }
                    $this->setTemplate(null);
                }
            }
        }
        return parent::getString($data);  // renderuj právě nastavenou šablonu neb použije renderer či fallback renderer

    }
}
