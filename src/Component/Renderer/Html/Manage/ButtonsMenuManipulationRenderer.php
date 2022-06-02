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
class ButtonsMenuManipulationRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel = NULL) {
        /** @var ItemViewModelInterface $viewModel */
        $menuItem = $viewModel->getHierarchyAggregate()->getMenuItem();
        return $this->renderButtons($menuItem);
    }

    protected function renderButtons(MenuItemInterface $menuItem) {
        $buttons[] = $this->expandButtons($this->getButtonAdd($menuItem), $this->classMap->get('Icons', 'icon.plus'));
        $buttons[] = $this->expandButtons($this->getButtonCut($menuItem).$this->getButtonCopy($menuItem), $this->classMap->get('Icons', 'icon.object'));
        return $buttons;
    }

    private function expandButtons($expandedButtons, $expandsionIconClass) {
        return
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsChangeView')],
                Html::tag('i', ['class'=>$expandsionIconClass])
                .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsChangeViewGroup')],
                    $expandedButtons
                )
            );
    }

    private function getButtonAdd(MenuItemInterface $menuItem) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Přidat sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addsiblings')])
            )
            .
            Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Přidat potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/addchild",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addchildren')])
            );
    }

    private function getButtonCut(MenuItemInterface $menuItem) {
        return  Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Vybrat k přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/cut",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.cut')])
            );
    }

    private function getButtonCopy(MenuItemInterface $menuItem) {
        return  Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Zkopírovat položku',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/copy",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.copy')])
            );
    }

}
