<?php
namespace  Component\Renderer\Html\Authored\Menu;

use Component\Renderer\Html\HtmlModelRendererAbstract;
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
class LevelWrapRenderer extends HtmlModelRendererAbstract {

    public function render($levelItemsHtml=NULL) {
        if ($this->viewModel->isEditableMenu()) {
            return $this->renderEditable($levelItemsHtml);
        } else {
            return $this->renderNoneditable($levelItemsHtml);
        }
    }        
    
    private function renderNoneditable($levelItemsHtml) {
        return Html::tag('ul', ['class'=>[
                                //$this->classMap->getClass('LevelWrap', 'ul'),
//                                $this->classMap->resolveClass($this->viewModel->isOnPath(), 'LevelWrap', 'ul.onpath', 'ul'),
                                $this->classMap->getClass('LevelWrap', 'ul.onpath'),
                                ]],
                            $levelItemsHtml);        
    }
    
    private function renderEditable($levelItemsHtml) {
        return Html::tag('ul', ['class'=>[
                                //$this->classMap->getClass('LevelWrap', 'ul'),
//                                $this->classMap->resolveClass($this->viewModel->isOnPath(), 'LevelWrap', 'ul.onpath', 'ul'),
                                $this->classMapEditable->getClass('LevelWrap', 'ul.onpath'),
                                ]],
                            $levelItemsHtml);
    }

}
