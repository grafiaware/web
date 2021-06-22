<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View;

use Pes\View\CompositeView;

use Component\ViewModel\ViewModelInterface;

/**
 * Description of ComponentAbstract
 *
 * @author pes2704
 */
abstract class CompositeComponentAbstract extends CompositeView {
    public function __construct(ViewModelInterface $viewModel=null) {
        if (isset($viewModel)) {
            $this->setData($viewModel);
        }
    }
}
