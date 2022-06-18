<?php
namespace Component\Renderer\Html\Content\Authored\Multipage;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Multipage\MultipageViewModelInterface;

use Component\View\Content\Authored\Multipage\MultipageComponent;
use Component\View\Content\Authored\AuthoredComponentAbstract;

use Pes\Text\Html;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class MultipageRenderer  extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var MultipageViewModelInterface $viewModel */
        $inner = (string) $viewModel->getContextVariable(MultipageComponent::CONTENT) ?? '';
        $buttonEditContent = (string) $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '';
        $multipage = $viewModel->getMultipage();
        if (isset($multipage)) {
            $html =
                Html::tag('div', ['class'=>$this->classMap->get('Template', 'div.templateMultipage')],
                    Html::tag('div', ['data-red-renderer'=>'MultipageRenderer', "data-red-datasource"=> "multipage {$multipage->getId()} for item {$multipage->getMenuItemIdFk()}"],
                        [$buttonEditContent, $inner]
                    )
                )
                ;
        }else {
            $html = '';
        }
        return $html;
    }
}