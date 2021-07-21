<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Generated;

use Component\Renderer\Html\HtmlRendererAbstract;

/**
 * Description of EmptyItemRenderer
 *
 * @author pes2704
 */
class EmptyItemRenderer extends HtmlRendererAbstract  {

    public function render(iterable $viewModel=NULL) {
        return "<p>Empty item</p>";
    }
}
