<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Status;

use Component\View\ComponentAbstract;
use Component\ViewModel\Status\StatusBoardViewModel;

/**
 * Description of StatusBoadComponent
 *
 * @author pes2704
 */
class StatusBoadComponent extends ComponentAbstract {

    // renderuje template, definováno v component kontejneru
    public function __construct(StatusBoardViewModel $viewModel) {
        $this->viewModel = $viewModel;
    }
}
