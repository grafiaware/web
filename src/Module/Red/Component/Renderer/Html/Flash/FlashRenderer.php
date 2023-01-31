<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\Renderer\Html\Flash;

use Red\Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Flash\FlashViewModelForRendererInterface;

use Pes\View\Renderer\RendererModelAwareInterface;

/**
 * Description of FlashRenderer
 *
 * @author pes2704
 */
class FlashRenderer extends HtmlRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var FlashViewModelForRendererInterface $viewModel */
        return $viewModel->getMessage();
    }
}
