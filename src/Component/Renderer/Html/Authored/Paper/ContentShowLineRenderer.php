<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

/**
 * Description of ContentShowLineRenderer
 *
 * @author pes2704
 */
class ContentShowLineRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        return
        '
<svg width="400" height="180">
  <rect x="0" y="40" width="350" height="5" style="fill:grey;opacity:0.5" />
  <rect x="50" y="17.5" rx="20" ry="20" width="250" height="50" style="fill:yellow;stroke:black;stroke-width:2;opacity:0.5" />
  <rect x="70" y="30" rx="20" ry="20" width="150" height="60" style="fill:red;stroke:black;stroke-width:5;opacity:1" />
  <rect x="90" y="0" width="5" height="100" style="fill:green;opacity:1" />
  Sorry, your browser does not support inline SVG.
</svg>
        ';

    }
}
