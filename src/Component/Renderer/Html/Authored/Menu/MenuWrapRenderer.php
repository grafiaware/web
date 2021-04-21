<?php
namespace  Component\Renderer\Html\Authored\Menu;
use Component\ViewModel\Authored\Menu\MenuViewModelInterface;

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
class MenuWrapRenderer extends MenuWrapRendererAbstract {

    public function render(iterable $data = NULL) {
        /** @var MenuViewModelInterface $viewModel */
        $viewModel = $this->viewModel;
        $menuLevelHtml = $this->getMenuLevelHtml($viewModel->getSubTreeItemModels());

        return Html::tag('ul', ['class'=>$this->classMap->getClass('MenuWrap', 'ul')],
            $menuLevelHtml
        );
    }
}
