<?php
namespace Red\Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
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
        if ($viewModel->presentEditableMenu()) {
//            $class[] = 'hierarchy';   // class hierarchy - nutná pro cascade.js, event listener pro výměny DriverComponent v editačním menu
            $class[] = 'navigation';   // class navigation - nutná pro cascade.js, event listener pro skrytí a zobrazení položek menu v needitačním režimu
        } else {
            $class[] = 'navigation';   // class navigation - nutná pro cascade.js, event listener pro skrytí a zobrazení položek menu v needitačním režimu
        }
        return Html::tag('div', ['class'=>$class], $innerHtml);
    }
}
