<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Status;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Status\FlashVieModel;

/**
 * Description of FlashRenderer
 *
 * @author pes2704
 */
class FlashRenderer extends HtmlRendererAbstract {
     
    public function render(iterable $data = NULL) {
        $this->renderPrivate($data);
    }

    private function renderPrivate(FlashVieModel $viewModel) {
        return $viewModel->getMessage();
    }
}
