<?php
namespace Component\View\Authored;

use Component\ViewModel\Authored\Paper\PresentedPaperViewModelInterface;
use Pes\View\Template\Exception\NoTemplateFileException;

use Pes\View\Template\PhpTemplate;

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
     *
     * @var PresentedPaperViewModelInterface
     */
    protected $viewModel;

    protected $renderer;

    /**
     *
     * @param PresentedPaperViewModelInterface $viewModel
     */
    public function __construct(PresentedPaperViewModelInterface $viewModel) {
        $this->viewModel = $viewModel;
    }

    public function getString($data = null) {
        $paperAggregate = $this->viewModel->getPaperAggregate();
        $paperTemplateName = $paperAggregate->getTemplate();
            $templatePath = PROJECT_PATH."public/web/templates/paper/".$paperTemplateName."/template.php";
            try {
                $this->setTemplate(new PhpTemplate($templatePath));
            } catch (NoTemplateFileException $noTemplExc) {
                if ($paperTemplateName) {
                    user_error("Neexistuje soubor šablony $templatePath", E_USER_WARNING);
                }
                $this->setTemplate(null);
            }

        return parent::getString();

    }
}
