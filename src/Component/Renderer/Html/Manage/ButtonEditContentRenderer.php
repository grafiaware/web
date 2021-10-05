<?php
namespace Component\Renderer\Html\Manage;

use Component\Renderer\Html\HtmlRendererAbstract;

use Pes\Text\Html;
use Pes\Type\ContextDataInterface;
use Component\View\Manage\ButtonEditContentComponent;

/**
 * Description of ToggleEditButtonRenderer
 *
 * @author pes2704
 */
class ButtonEditContentRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel = NULL) {
        /** @var ContextDataInterface $viewModel */
        $typeFk = $viewModel->offsetGet(ButtonEditContentComponent::CONTEXT_TYPE_FK);
        $itemId = $viewModel->offsetGet(ButtonEditContentComponent::CONTEXT_ITEM_ID);
        $userPerformActionWithContent = $viewModel->offsetGet(ButtonEditContentComponent::CONTEXT_USER_PERFORM_ACTION);
        if ($userPerformActionWithContent) {
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
                        'class'=>$this->classMap->resolveClass($userPerformActionWithContent, 'PaperButtons', 'div.offEditMode button', 'div.editMode button'),
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
