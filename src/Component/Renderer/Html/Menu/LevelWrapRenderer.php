<?php
namespace  Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Pes\Text\Html;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Svisle
 *
 * @author pes2704
 */
class LevelWrapRenderer extends HtmlRendererAbstract {

    public function render($levelItemsHtml=NULL) {
        return Html::tag('ul', ['class'=>[
                                //$this->classMap->get('LevelWrap', 'ul'),
//                                $this->classMap->resolve($this->viewModel->isOnPath(), 'LevelWrap', 'ul.onpath', 'ul'),
                                $this->classMap->get('LevelWrap', 'ul'),
                                ]],
                            $levelItemsHtml);
    }

}
