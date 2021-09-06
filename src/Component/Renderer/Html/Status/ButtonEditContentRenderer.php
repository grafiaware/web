<?php
namespace Component\Renderer\Html\Status;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\StatusViewModelInterface;

use Pes\Text\Html;
use Pes\Type\ContextDataInterface;

/**
 * Description of ToggleEditButtonRenderer
 *
 * @author pes2704
 */
class ButtonEditContentRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel = NULL) {
        /** @var ContextDataInterface $viewModel */
        $typeFk = $viewModel->offsetGet('typeFk');
        $itemId = $viewModel->offsetGet('itemId');
        if ($viewModel->offsetGet('userPerformActionWithContent')) {
            $tooltip = 'Vypnout editaci';
            $action = "red/v1/itemaction/$typeFk/$itemId/remove";
        } else {
            $tooltip = 'Zapnout editaci';
            $action = "red/v1/itemaction/$typeFk/$itemId/add";
        }
        return
            Html::tag('div', ['class'=>$this->classMap->getClass('PaperButtons', 'div.editMode')], //tlačítko "tužka" pro zvolení editace
                Html::tag('form', ['method'=>'POST', 'action'=>$action],
                    Html::tag('button', [
                        'class'=>$this->classMap->getClass('PaperButtons', 'div.editMode button'),
                        'data-tooltip' => $tooltip,
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
