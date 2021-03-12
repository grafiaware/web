<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Flash;

use Component\View\ComponentAbstract;
use Component\ViewModel\Flash\FlashViewModelInterface;

/**
 * Description of FlashComponent
 *
 * @author pes2704
 */
class FlashComponent extends ComponentAbstract {

    /**
     *
     * @param FlashViewModelInterface $viewModel
     */
    public function __construct(FlashViewModelInterface $viewModel) {
        $this->setData($viewModel);
    }
}
