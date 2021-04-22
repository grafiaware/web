<?php
namespace  Component\Renderer\Html\Authored\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
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
class MenuWrapEditableRenderer extends MenuWrapRendererAbstract {

    public function render($data = NULL) {
        /** @var MenuViewModelInterface $viewModel */
        $viewModel = $this->viewModel;
        $menuLevelHtml = $this->getMenuHtml($viewModel->getSubTreeItemModels());

        return
        Html::tag('form', [],
            Html::tag('ul', ['class'=>$this->classMap->getClass('MenuWrap', 'ul')],
                $menuLevelHtml
            )
        );
    }
}
