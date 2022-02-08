<?php
namespace Component\Renderer\Html\Manage;

use Component\Renderer\Html\HtmlRendererAbstract;

use Pes\Text\Html;
use Pes\Type\ContextDataInterface;
use Component\View\Manage\ToggleEditContentButtonComponent;
use Component\ViewModel\Authored\AuthoredViewModelInterface;
use Red\Middleware\Redactor\Controler\UserActionControler;
/**
 * Description of ToggleEditButtonRenderer
 *
 * @author pes2704
 */
class ToggleEditContentButtomRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel = NULL) {
        /** @var AuthoredViewModelInterface $viewModel */
        $userPerformActionWithContent = $viewModel->userPerformActionWithItem();
        if ($userPerformActionWithContent) {
            $tooltip = 'Vypnout editaci';
            $action = "red/v1/itemaction/{$viewModel->getItemType()}/{$viewModel->getItemId()}/remove";
        } else {
            $action = $viewModel->getItemAction();
            if (isset($action)) {
                $editor = $action->getEditorLoginName() ?? '';
                $disabled = 'disabled';
            }
            $tooltip = 'Zapnout editaci';
            $action = "red/v1/itemaction/{$viewModel->getItemType()}/{$viewModel->getItemId()}/add";
        }

        return
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.editMode')], //tlačítko "tužka" pro zvolení editace
                Html::tag('form', ['method'=>'POST', 'action'=>$action],
                    Html::tag('button', [
                        'class'=>[$this->classMap->resolve($userPerformActionWithContent, 'Buttons', 'div.offEditMode button', 'div.editMode button'), $disabled ?? ''],

                        'data-tooltip' => $tooltip,
                        'name' => UserActionControler::FORM_USER_ACTION_EDIT_CONTENT,
                        'value' => '',
                        'type' => 'submit',
                        'formtarget' => '_self',
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.editMode')])
                    )
                )
            );
    }
}
