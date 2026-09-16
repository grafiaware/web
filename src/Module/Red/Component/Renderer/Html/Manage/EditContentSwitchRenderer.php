<?php
namespace Red\Component\Renderer\Html\Manage;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Content\Authored\AuthoredViewModelInterface;

use Pes\Core\Text\Html;

/**
 * Description of ToggleEditButtonRenderer
 *
 * @author pes2704
 */
class EditContentSwitchRenderer extends HtmlRendererAbstract {
    public function render(?iterable $viewModel = NULL) {
        /** @var AuthoredViewModelInterface $viewModel */
        $menuItemId = $viewModel->getMenuItem()->getId();
        $userPerformActionWithContent = $viewModel->userPerformItemAction();
        $inTrash = $viewModel->isInTrash();
        $editor = '';
        $disabled = false;

        if ($inTrash && !$userPerformActionWithContent) {
            $disabled = true;
            $tooltip = "Položka je v koši. Pro editaci ji přesuňte do jiného menu.";
            $action = "red/v1/itemaction/$menuItemId/add";
        } elseif ($userPerformActionWithContent) {
            $tooltip = "Vypnout editaci";
            $action = "red/v1/itemaction/$menuItemId/remove";
        } else {
            $itemAction = $viewModel->getItemAction();
            if (isset($itemAction)) {
                $editor = $itemAction->getEditorLoginName() ?? '';
                $disabled = true;
            }
            $tooltip = $editor ? "Nelze zapnout editaci (Obsah upravuje $editor)." :  "Zapnout editaci";
            $action = "red/v1/itemaction/$menuItemId/add";
        }
        $buttonClassKey = $userPerformActionWithContent
            ? 'button.offEditMode'
            : ($disabled ? 'button.editMode.disabled' : 'button.editMode');
        return
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.itemAction')], //tlačítko "tužka" pro zvolení editace
                Html::tag('form', ['class'=>'apiAction', 'method'=>'POST', 'action'=>$action],  // method POST = fallback bez JS; menuSwap.js odchytí .apiAction a pošle PUT
                    // class apiAction: selektor v menuSwap.js (listenFormsWithApiAction)
                    [
                        Html::tag('button', [
                            'class'=>$this->classMap->get('Buttons', $buttonClassKey),
                            'data-tooltip' => $tooltip,
                            'data-position' => 'bottom center',
                            'type' => $disabled ? 'button' : 'submit',
                            'disabled' => $disabled,
                            'formtarget' => '_self',
                            ],
                            Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.editMode')])
                        )
                    ]
                )
            );
    }

}
