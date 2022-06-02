<?php
namespace Component\Renderer\Html\Manage;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;

use Pes\Text\Html;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsPasteRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel = NULL) {
                if($viewModel->isCutted()) {
                    $buttonsHtml[] = $this->renderCuttedItemButtons($menuItem);
                } else {
                    $buttonsHtml[] = $this->getButtonPasteSiebling($menuItem);
                    $buttonsHtml[] = $this->getButtonPasteChild($menuItem);
                    $buttonsHtml[] = $this->renderCuttedItemButtons($menuItem);
                }
        return implode("", $buttonsHtml);
    }

    protected function renderButtonsCutted(MenuItemInterface $menuItem) {
        $buttons[] = $this->getButtonCutted($menuItem);
        return $buttons;
    }

    private function getButtonPasteSiebling(MenuItemInterface $menuItem) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit jako sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/paste",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addsiblings')])
            );
    }

    private function getButtonPasteChild(MenuItemInterface $menuItem) {
            Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit jako potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/pastechild",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addchildren')])
            );
    }

    private function getButtonCutted(MenuItemInterface $menuItem) {
        return  Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Zrušit přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/cutescape",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.cutted')])
            );
    }
}
