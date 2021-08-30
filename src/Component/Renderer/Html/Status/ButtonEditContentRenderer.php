<?php
namespace Component\Renderer\Html\Status;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\StatusViewModelInterface;

use Pes\Text\Html;

/**
 * Description of ToggleEditButtonRenderer
 *
 * @author pes2704
 */
class ButtonEditContentRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel = NULL) {
        /** @var StatusViewModelInterface $viewModel */
        return
            Html::tag('div', ['class'=>$this->classMap->getClass('PaperButtons', 'div.editMode')], //tlačítko "tužka" pro zvolení editace
                Html::tag('form', ['method'=>'POST', 'action'=>''],
                    Html::tag('button', [
                        'class'=>$this->classMap->getClass('PaperButtons', 'div.editMode button'),
                        'data-tooltip' => 'Článek můžete editovat',
                        'name' => 'edit_article',
                        'value' => '',
                        'type' => 'submit',
                        'formtarget' => '_self',
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('PaperButtons', 'div.editMode i')])
                    )
                )
            );
    }
}
