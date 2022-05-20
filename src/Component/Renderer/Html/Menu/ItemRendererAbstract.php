<?php
namespace Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Model\Entity\MenuItemInterface;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;

use Pes\Text\Html;

/**
 * Description of ItemRendererAbstract
 *
 * @author pes2704
 */
abstract class ItemRendererAbstract extends HtmlRendererAbstract {

    /**
     *
     * @var ItemViewModelInterface
     */
    protected $viewModel;

    protected function semafor(MenuItemInterface $menuItem) {
        if ($this->viewModel->isMenuEditable()) {
            $active =$menuItem->getActive();
            $html = Html::tag('span', ['class'=>$this->classMap->get('Item', 'semafor')],
                        Html::tag('i', [
                            'class'=> $this->classMap->resolve($active, 'Icons', 'semafor.published', 'semafor.notpublished'),
                            'title'=> $active ? "published" :  "not published"
                            ])
                    );
        } else {
            $html = "";
        }
        return $html;
    }

}
