<?php
namespace Component\Renderer\Html\Authored\Multipage;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Multipage\MultipageViewModelInterface;

use Component\View\Authored\Multipage\MultipageComponent;

use Pes\Text\Html;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class MultipageRenderer  extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var MultipageViewModelInterface $viewModel */
        $inner = (string) $viewModel->getContextVariable(MultipageComponent::CONTEXT_TEMPLATE) ?? '';
        $buttonEditContent = (string) $viewModel->getContextVariable(MultipageComponent::CONTEXT_BUTTON_EDIT_CONTENT) ?? '';
        $html =
                //Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.templateMultipage')],
                    Html::tag('div', ['data-red-renderer'=>'MultipageRenderer', "data-red-datasource"=> "multipage {$viewModel->getMultipage()->getId()} for item {$viewModel->getMultipage()->getMenuItemIdFk()}"],
                        [$buttonEditContent, $inner]
                    )
                //)
                ;
        return $html ?? '';
    }
}