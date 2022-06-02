<?php
namespace Component\Renderer\Html\Manage;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\AuthoredViewModelInterface;
use Red\Middleware\Redactor\Controler\UserActionControler;

use Pes\Text\Html;

/**
 * Description of ToggleEditButtonRenderer
 *
 * @author pes2704
 */
class EditContentSwitchRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel = NULL) {
        /** @var AuthoredViewModelInterface $viewModel */
        $contentType = $viewModel->getMenuItem()->getTypeFk();
        $menuItemId = $viewModel->getMenuItem()->getId();
        $userPerformActionWithContent = $viewModel->userPerformAuthoredContentAction();
        $editor = '';
        $disabled = '';
        $itemAction = $viewModel->getAuthoredContentAction();

        if($userPerformActionWithContent) {
            if (isset($itemAction)) {
                $editor = $itemAction->getEditorLoginName() ?? '';
            }
            $tooltip = $editor ? "Vypnout editaci (Obsah upravuje $editor)." :  "Vypnout editaci";
            $action = "red/v1/itemaction/$contentType/$menuItemId/remove";
        } else {
            $itemAction = $viewModel->getAuthoredContentAction();
            if (isset($itemAction)) {
                $editor = $itemAction->getEditorLoginName() ?? '';
                $disabled = 'disabled';
            }
            $tooltip = $editor ? "Zapnout editaci (Obsah upravuje $editor)." :  "Zapnout editaci";
            $action = "red/v1/itemaction/$contentType/$menuItemId/add";
        }
        return
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.editMode')], //tlačítko "tužka" pro zvolení editace
                Html::tag('form', ['method'=>'POST', 'action'=>$action],
                    [
                        //Html::tag('p', [], isset($editor) ? "Obsah upravuje $editor." : ''),
                        Html::tag('button', [
                            'class'=>$this->classMap->resolve($userPerformActionWithContent, 'Buttons', 'button.offEditMode',  $disabled ? 'button.editMode.disabled':'button.editMode'),
                            'data-tooltip' => $tooltip,
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
