<?php
namespace Component\Renderer\Html\Authored\Multipage;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Multipage\MultipageViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\PaperContentInterface;

use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;

use Pes\Text\Html;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class MultipageRenderer  extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var MultipageViewModelInterface $viewModel */
        $inner = (string) $viewModel->getContextVariable('template') ?? '';
        $buttonEditContent = (string) $viewModel->getContextVariable('buttonEditContent') ?? '';
        $html =
                Html::tag('div', ['data-red-renderer'=>'MultipageRenderer', "data-red-datasource"=> "multipage {$viewModel->getMultipage()->getId()} for item {$viewModel->getMultipage()->getMenuItemIdFk()}"],
                        [$buttonEditContent, $inner]
                );
        return $html ?? '';
    }
}