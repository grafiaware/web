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

    /**
     *
     * @var MenuViewModelInterface
     */
    protected $viewModel;

    public function render(iterable $viewModel=NULL) {
        /** @var MenuViewModelInterface $viewModel */
        $menuLevelHtml = $this->getMenuHtml($viewModel->getSubTreeItemModels());
        if ($viewModel->isEditableMenu()) {
            return
            Html::tag('form', [],
                Html::tag('ul', ['class'=>$this->classMap->getClass('MenuWrap', 'ul')],
                    $menuLevelHtml
                )
            );
        } else {
            return Html::tag('ul', ['class'=>$this->classMap->getClass('MenuWrap', 'ul')],
                $menuLevelHtml
            );
        }
    }
}
