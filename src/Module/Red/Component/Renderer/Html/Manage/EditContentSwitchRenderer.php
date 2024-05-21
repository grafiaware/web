<?php
namespace Red\Component\Renderer\Html\Manage;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Content\Authored\AuthoredViewModelInterface;

use Pes\Text\Html;

/**
 * Description of ToggleEditButtonRenderer
 *
 * @author pes2704
 */
class EditContentSwitchRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel = NULL) {
        /** @var AuthoredViewModelInterface $viewModel */
        $menuItemId = $viewModel->getMenuItem()->getId();
        $userPerformActionWithContent = $viewModel->userPerformItemAction();
        $editor = '';
        $disabled = '';
        $itemAction = $viewModel->getItemAction();

        if($userPerformActionWithContent) {
            $tooltip = "Vypnout editaci";
            $action = "red/v1/itemaction/$menuItemId/remove";
        } else {
            $itemAction = $viewModel->getItemAction();
            if (isset($itemAction)) {
                $editor = $itemAction->getEditorLoginName() ?? '';
                $disabled = 'disabled';
            }
            $tooltip = $editor ? "Nelze zapnout editaci (Obsah upravuje $editor)." :  "Zapnout editaci";
            $action = "red/v1/itemaction/$menuItemId/add";
        }
        return
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.itemAction')], //tlačítko "tužka" pro zvolení editace
                Html::tag('form', ['class'=>'apiAction', 'method'=>'POST', 'action'=>$action],  //TODO: hodnota class se používá v cascade.js pro selektor -> do konfigurace
                    [
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
