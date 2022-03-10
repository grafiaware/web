<?php
namespace Component\Renderer\Html\Authored\Multipage;

use Component\Renderer\Html\Authored\AuthoredRendererAbstract;
use Component\ViewModel\Authored\Multipage\MultipageViewModelInterface;
use Component\View\MenuItem\Authored\Multipage\MultipageComponent;
use Component\View\MenuItem\Authored\AuthoredComponentAbstract;

use Red\Model\Entity\MultipageInterface;

use Pes\Text\Html;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class MultipageRendererEditable  extends AuthoredRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var MultipageViewModelInterface $viewModel */
        $multipage = $viewModel->getMultipage();

        $html =
                Html::tag('div', ['class'=>$this->classMap->get('Template', 'div.templateMultipage')],
                  Html::tag('div', ['data-red-renderer'=>'MultipageRendererEditable', "data-red-datasource"=> "multipage {$multipage->getId()} for item {$multipage->getMenuItemIdFk()}"],
                        [
                            $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '',
                            $this->renderSelectTemplate($viewModel),
                            $this->renderRibbon($viewModel),
                            $viewModel->getContextVariable(MultipageComponent::CONTENT) ?? '',
                        ]
                    )
                );
        return $html ?? '';
    }

    /**
     * Nemá buttony
     *
     * @param MultipageViewModelInterface $viewModel
     * @return array
     */
    protected function renderContentControlButtons(MultipageViewModelInterface $viewModel): array {
        return [];
    }

}