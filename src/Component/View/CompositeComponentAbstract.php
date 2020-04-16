<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View;

use Pes\View\CompositeView;
use Pes\View\Renderer\RendererInterface;

use Component\ViewModel\ViewModelInterface;

/**
 * Description of ComponentAbstract
 *
 * @author pes2704
 */
abstract class CompositeComponentAbstract extends CompositeView {
    
    /**
     *
     * @var ViewModelInterface
     */
    protected $viewModel;

    /**
     *
     * @var RendererInterface
     */
    protected $renderer;

    public function getString($data=null) {
        $this->setData($this->viewModel);
        return parent::getString($data);
    }
}
