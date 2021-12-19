<?php
namespace Component\Renderer\Html\Manage;

use Component\Renderer\Html\HtmlRendererAbstract;

use Pes\Text\Html;
use Pes\Type\ContextDataInterface;
use Component\View\Manage\ToggleEditContentButtonComponent;

/**
 * Description of ToggleEditButtonRenderer
 *
 * @author pes2704
 */
class ToggleEditContentButtomRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel = NULL) {
        /** @var ContextDataInterface $viewModel */
        $typeFk = $viewModel->offsetGet(ToggleEditContentButtonComponent::CONTEXT_TYPE_FK);
        $itemId = $viewModel->offsetGet(ToggleEditContentButtonComponent::CONTEXT_ITEM_ID);
        $userPerformActionWithContent = $viewModel->offsetGet(ToggleEditContentButtonComponent::CONTEXT_USER_PERFORM_ACTION);
        if ($userPerformActionWithContent) {
            $tooltip = 'Vypnout editaci';
            $action = "red/v1/itemaction/$typeFk/$itemId/remove";
        } else {
            $tooltip = 'Zapnout editaci';
            $action = "red/v1/itemaction/$typeFk/$itemId/add";
        }
        return
            Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.editMode')], //tlačítko "tužka" pro zvolení editace
                Html::tag('form', ['method'=>'POST', 'action'=>$action],
                    Html::tag('button', [
                        'class'=>$this->classMap->resolve($userPerformActionWithContent, 'PaperButtons', 'div.offEditMode button', 'div.editMode button'),
                        'data-tooltip' => $tooltip,
                        'name' => 'edit_article',
                        'value' => '',
                        'type' => 'submit',
                        'formtarget' => '_self',
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('PaperButtons', 'div.editMode i')])
                    )
                )
            );
    }
}
