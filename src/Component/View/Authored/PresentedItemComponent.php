<?php
namespace Component\View\Authored;

use Component\ViewModel\Authored\Paper\PresentedPaperViewModelInterface;
use Model\Entity\PaperContentInterface;

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
class PresentedItemComponent extends AuthoredComponentAbstract implements AuthoredComponentInterface {

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
        if ($paperTemplateName ?? false) {
            $contents = [];
            foreach ($paperAggregate->getPaperContentsArray() as $id=>$paperContent) {
                /** @var PaperContentInterface $paperContent */
                if (true OR $paperContent->getActive() AND $paperContent->getActual()) {
                    $contents[] = [
                        'templateName' => $paperContent->getTemplate(),
                        'content' => $paperContent->getContent(),
                    ];
                }
            }
            $this->setTemplate(new PhpTemplate(PROJECT_PATH."public/web/templates/paper/".$paperTemplateName."/template.php"))
                ->setData([
                    'paperTemplateName' => $paperTemplateName,
                    'paperAggregate' => $paperAggregate,
                ]);
        }
        return parent::getString();

    }
}
