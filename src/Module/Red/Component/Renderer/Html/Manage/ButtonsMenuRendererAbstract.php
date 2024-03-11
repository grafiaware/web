<?php
namespace Red\Component\Renderer\Html\Manage;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Menu\ItemViewModelInterface;

use Pes\Text\Html;

/**
 * Description of ButtonsMenuRendererAbstract
 *
 * @author pes2704
 */
abstract class ButtonsMenuRendererAbstract  extends HtmlRendererAbstract {

    public function render(iterable $viewModel = NULL) {
        return $this->renderButtons($viewModel);
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

    protected function getButtonActive(ItemViewModelInterface $viewModel) {
        $active = $viewModel->isActive();
        return Html::tag('button',
                ['class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=> $active ? 'Nepublikovat' : 'Publikovat',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/menu/{$viewModel->getUid()}/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMap->resolve($active, 'Icons', 'icon.notpublish', 'icon.publish')])
            );
    }

    protected function getButtonTrash(ItemViewModelInterface $viewModel) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Odstranit položku',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$viewModel->getUid()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.movetotrash')])
            );
    }

    protected function getButtonAdd(ItemViewModelInterface $viewModel) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Přidat sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$viewModel->getUid()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addsiblings')])
            );
    }

    protected function getButtonAddChild(ItemViewModelInterface $viewModel) {
            return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Přidat potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$viewModel->getUid()}/addchild",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addchildren')])
            );
    }

    protected function getButtonPaste(ItemViewModelInterface $viewModel) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit vybrané jako sourozence',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$viewModel->getUid()}/paste",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addsiblings')])
            );
    }

    protected function getButtonPasteChild(ItemViewModelInterface $viewModel) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit vybrané jako potomka',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$viewModel->getUid()}/pastechild",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addchildren')])
            );
    }

    protected function getButtonCut(ItemViewModelInterface $viewModel) {
        return  Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Vybrat k přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$viewModel->getUid()}/cut",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.cut')])
            );
    }

    protected function getButtonCopy(ItemViewModelInterface $viewModel) {
        return  Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Vybrat ke zkopírování',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$viewModel->getUid()}/copy",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.copy')])
            );
    }

    protected function getButtonCutCopyEscape(ItemViewModelInterface $viewModel) {
        return  Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Zrušit přesunutí nebo kopírování',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$viewModel->getUid()}/cutcopyescape",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.cutted')])
            );
    }

    protected function getButtonDelete(ItemViewModelInterface $viewModel) {
        return
            Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Trvale odstranit',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$viewModel->getUid()}/delete",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icons'),],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.delete'),])
                        .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.exclamation'),])
                )
            );
    }
}
