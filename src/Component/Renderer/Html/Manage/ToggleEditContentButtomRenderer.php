<?php
namespace Component\Renderer\Html\Manage;

use Component\Renderer\Html\HtmlRendererAbstract;

use Pes\Text\Html;
use Pes\Type\ContextDataInterface;
use Component\View\Manage\ToggleEditContentButtonComponent;
use Component\ViewModel\Authored\AuthoredViewModelInterface;

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
            $tooltip = 'Zapnout editaci';
            $action = "red/v1/itemaction/{$viewModel->getItemType()}/{$viewModel->getItemId()}/add";
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
