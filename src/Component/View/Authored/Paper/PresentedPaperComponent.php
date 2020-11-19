<?php
namespace Component\View\Authored\Paper;

use Component\View\Authored\Paper\AuthoredComponentAbstract;

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
class PresentedPaperComponent extends AuthoredComponentAbstract {

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

}
