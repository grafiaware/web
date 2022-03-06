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
class ToggleEditContentButtonRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel = NULL) {
        /** @var AuthoredViewModelInterface $viewModel */
        $userPerformActionWithContent = $viewModel->userPerformAuthoredContentAction();
        $disabled = '';
        if ($userPerformActionWithContent) {
            $tooltip = 'Vypnout editaci';
            $action = "red/v1/itemaction/{$viewModel->getAuthoredContentType()}/{$viewModel->getAuthoredContentId()}/remove";
        } else {
            $action = $viewModel->getAuthoredContentAction();
            if (isset($action)) {
                $editor = $action->getEditorLoginName() ?? '';
                $disabled = 'disabled';
            }
            $tooltip = 'Zapnout editaci';
            $action = "red/v1/itemaction/{$viewModel->getAuthoredContentType()}/{$viewModel->getAuthoredContentId()}/add";
        }

        return
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.editMode')], //tlačítko "tužka" pro zvolení editace
                Html::tag('form', ['method'=>'POST', 'action'=>$action],
                    [
                        //Html::tag('p', [], isset($editor) ? "Obsah upravuje $editor." : ''),
                        Html::tag('button', [
                            'class'=>$this->classMap->resolve($userPerformActionWithContent, 'Buttons', 'button.offEditMode',  $disabled ? 'button.editMode.disabled':'button.editMode'),

                            'data-tooltip' => isset($editor) ? "Obsah upravuje $editor." : $tooltip,
                            'name' => UserActionControler::FORM_USER_ACTION_EDIT_CONTENT,
                            'value' => '',
                            'type' => $disabled ? 'button':'submit',
                            'formtarget' => '_self',
                            ],
                            Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.editMode')])
                        )
                    ]
                )
            );
    }
}
