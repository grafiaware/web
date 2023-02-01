<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Red\Component\Renderer\Html\Menu;

use Red\Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Menu\MenuViewModelInterface;
use Red\Component\View\Menu\MenuComponentInterface;

use Pes\Text\Html;

/**
 * Description of MenuRenderer
 *
 * @author pes2704
 */
class MenuRenderer extends HtmlRendererAbstract {

    /**
     *
     * @var MenuViewModelInterface
     */
    protected $viewModel;

    public function render(iterable $viewModel=NULL) {
        /** @var MenuViewModelInterface $viewModel */
        $innerHtml = [];
        if ($viewModel->offsetExists(MenuComponentInterface::TOGGLE_EDIT_MENU_BUTTON)) {
            $innerHtml[] = $viewModel->offsetGet(MenuComponentInterface::TOGGLE_EDIT_MENU_BUTTON);
        }
        if ($viewModel->offsetExists(MenuComponentInterface::MENU)) {
            $innerHtml[] = $viewModel->offsetGet(MenuComponentInterface::MENU);
        }
        return Html::tag('div', [], $innerHtml);
    }
}
