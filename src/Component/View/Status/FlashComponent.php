<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Status;

use Component\View\ComponentAbstract;
use Component\ViewModel\Status\FlashVieModel;

/**
 * Description of FlashComponent
 *
 * @author pes2704
 */
class FlashComponent extends ComponentAbstract {

    /**
     *
     * @param FlashVieModel $viewModel
     */
    public function __construct(FlashVieModel $viewModel) {
        $this->viewModel = $viewModel;
    }

}
