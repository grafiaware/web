<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Status;

use Component\Renderer\Html\HtmlModelRendererAbstract;
use Component\ViewModel\Status\FlashVieModel;

use Pes\View\Renderer\RendererModelAwareInterface;

/**
 * Description of LoginRenderer
 *
 * @author pes2704
 */
class LoginRenderer extends HtmlModelRendererAbstract implements RendererModelAwareInterface {

    // NEPOUŽITO - NESMYSLNÝ VIEWMODEL
    public function render(iterable $data = NULL) {
        /** @var FlashVieModel $viewModel */
        return $viewModel->getMessage();
    }
}
