<?php
namespace Component\Renderer\Html\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\PaperContentInterface;

use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;

use Pes\Text\Html;

use Component\View\Authored\Paper\PaperComponent;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperRenderer  extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paperAggregate = $viewModel->getPaper();  // vrací PaperAggregate
        $inner = (string) $viewModel->getContextVariable(PaperComponent::CONTEXT_TEMPLATE) ?? '';
        $buttonEditContent = (string) $viewModel->getContextVariable(PaperComponent::CONTEXT_BUTTON_EDIT_CONTENT) ?? '';
        $html =
                Html::tag('article', ['data-red-renderer'=>'PaperRenderer', "data-red-datasource"=> "paper {$paperAggregate->getId()} for item {$paperAggregate->getMenuItemIdFk()}"],
                        [$buttonEditContent, $inner]
                );
        return $html ?? '';
    }
}