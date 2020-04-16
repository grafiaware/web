<?php
namespace Component\View\Authored;

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
}
