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

    public function render($data=NULL) {
        if ($this->viewModel->isEditableMenu()) {
            return $this->renderEditable();
        } else {
            return $this->renderNoneditable();
        }
    }

    public function renderNoneditable(iterable $data = NULL) {
        /** @var MenuViewModelInterface $viewModel */
        $viewModel = $this->viewModel;
        $menuLevelHtml = $this->getMenuHtml($viewModel->getSubTreeItemModels());

        return Html::tag('ul', ['class'=>$this->classMap->getClass('MenuWrap', 'ul')],
            $menuLevelHtml
        );
    }

    public function renderEditable($data = NULL) {
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
