<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html;

use Pes\View\Renderer\RendererModelAwareInterface;

/**
 * Description of HtmlModelRendererAbstract
 *
 * @author pes2704
 */
abstract class HtmlModelRendererAbstract extends HtmlRendererAbstract implements RendererModelAwareInterface {

    public function setViewModel($viewModel) {
        $this->viewModel = $viewModel;
    }

}
