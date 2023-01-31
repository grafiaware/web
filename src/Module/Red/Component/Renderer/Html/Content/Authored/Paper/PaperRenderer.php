<?php
namespace Red\Component\Renderer\Html\Content\Authored\Paper;

use Red\Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\PaperSectionInterface;

use Red\Component\Renderer\Html\Content\Authored\Paper\HeadlineRenderer;

use Pes\Text\Html;

use Red\Component\View\Content\Authored\Paper\PaperComponent;
use Red\Component\View\Content\Authored\AuthoredComponentAbstract;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperRenderer  extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paperAggregate = $viewModel->getPaper();  // vrací PaperAggregate
        if (isset($paperAggregate)) {
            $html = Html::tag('div', ['class'=>$this->classMap->get('Template', 'div.templatePaper')],
                        Html::tag('article', ['data-red-renderer'=>'PaperRenderer', "data-red-datasource"=> "paper {$paperAggregate->getId()} for item {$paperAggregate->getMenuItemIdFk()}"],
                            [
                                $viewModel->getContextVariable(PaperComponent::BUTTON_EDIT_CONTENT) ?? '',
                                $viewModel->getContextVariable(PaperComponent::CONTENT) ?? '',
                            ]
                    )
                );
        } else {
            $html = '';
        }
        return $html;
    }
}