<?php
namespace Red\Component\Renderer\Html\Manage;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Model\Entity\MenuItemInterface;

use Pes\Text\Html;

/**
 * Description of ButtonsMenuRendererAbstract
 *
 * @author pes2704
 */
abstract class ButtonsMenuRendererAbstract  extends HtmlRendererAbstract {

    public function render(iterable $viewModel = NULL) {
        /** @var ItemViewModelInterface $viewModel */
        $menuItem = $viewModel->getHierarchyAggregate()->getMenuItem();
        return $this->renderButtons($menuItem);
    }

    protected function expandButtons($expandedButtons, $expandsionIconClass) {
        return
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsChangeView')],
                Html::tag('i', ['class'=>$expandsionIconClass])
                .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsChangeViewGroup')],
                    $expandedButtons
                )
            );
    }

    protected function getButtonActive(MenuItemInterface $menuItem) {
        $active = $menuItem->getActive();
        return Html::tag('button',
                ['class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=> $active ? 'Nepublikovat' : 'Publikovat',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/menu/{$menuItem->getUidFk()}/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMap->resolve($active, 'Icons', 'icon.notpublish', 'icon.publish')])
            );
    }

    protected function getButtonTrash(MenuItemInterface $menuItem) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Odstranit položku',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.movetotrash')])
            );
    }

    protected function getButtonAdd(MenuItemInterface $menuItem) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Přidat sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addsiblings')])
            );
    }

    protected function getButtonAddChild(MenuItemInterface $menuItem) {
            return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Přidat potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/addchild",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addchildren')])
            );
    }

    protected function getButtonPaste(MenuItemInterface $menuItem) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit vybrané jako sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/paste",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addsiblings')])
            );
    }

    protected function getButtonPasteChild(MenuItemInterface $menuItem) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit vybrané jako potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/pastechild",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addchildren')])
            );
    }

    protected function getButtonCut(MenuItemInterface $menuItem) {
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

    protected function getButtonCopy(MenuItemInterface $menuItem) {
        return  Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Vybrat ke zkopírování',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/copy",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.copy')])
            );
    }

    protected function getButtonCutCopyEscape(MenuItemInterface $menuItem) {
        return  Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Zrušit přesunutí nebo kopírování',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/cutcopyescape",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.cutted')])
            );
    }

    protected function getButtonDelete(MenuItemInterface $menuItem) {
        return
            Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Trvale odstranit',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/delete",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icons'),],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.delete'),])
                        .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.exclamation'),])
                )
            );
    }
}
