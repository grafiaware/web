<?php
namespace  Component\Renderer\Html\Menu;
use Component\ViewModel\Menu\MenuViewModelInterface;
use Component\View\Menu\MenuComponent;
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
        // MenuViewModel exteduje ContextData, má getIterator(), která iteruje ContextData, v ContextData jsou vyrenderované komponentní views
        $levelItemsHtml = "";
        foreach ($viewModel as $itemHtml) {
            $levelItemsHtml .= $itemHtml;
        }
        $html = [];
        $html[] = $viewModel->offsetExists(MenuComponent::TOGGLE_EDIT_MENU_BUTTON) ? $viewModel->offsetGet(MenuComponent::TOGGLE_EDIT_MENU_BUTTON) : "";
        $html[] = Html::tag('ul', ['class'=>$this->classMap->get('MenuWrap', 'ul')],$levelItemsHtml);
        return implode('', $html);
    }

}
