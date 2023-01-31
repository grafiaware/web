<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\View\Flash;

use Red\Component\View\ComponentCompositeAbstract;
use Red\Component\ViewModel\Flash\FlashViewModelInterface;

/**
 * Description of FlashComponent
 *
 * @author pes2704
 */
class FlashComponent extends ComponentCompositeAbstract {

    /**
     * @var FlashViewModelInterface
     */
    protected $contextData;

}
