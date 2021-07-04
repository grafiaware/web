<?php
namespace Component\Renderer\Html\Authored\Paper;

use Red\Model\Entity\PaperAggregatePaperContentInterface;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Pes\Text\Html;
use Pes\View\Renderer\ImplodeRenderer;

use Component\View\Authored\Paper\ButtonsForm\PaperTemplateButtonsForm;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperRenderer  extends PaperRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paperAggregate = $viewModel->getPaper();  // vrací PaperAggregate
        if (isset($paperAggregate)) { // paper je načten pokud menu item je aktivní (publikovaný) nebo režim je editační
            if ($viewModel->userCanEdit()) {  // editační režim a uživatel má právo editovat
                $articleButtonForms = $this->renderPaperButtonsForm($paperAggregate);

                $headline = $this->renderHeadlineEditable($paperAggregate);
                $perex = $this->renderPerexEditable($paperAggregate);
                $contents = ($paperAggregate instanceof PaperAggregatePaperContentInterface) ? $this->renderContentsEditable($paperAggregate) : "";

                $html = Html::tag('article', ['data-red-renderer'=>'ArticleEditableRenderer', "data-red-datasource"=> "paper {$paperAggregate->getId()} for item {$paperAggregate->getMenuItemIdFk()}"],
                            $articleButtonForms
                            .Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/{$paperAggregate->getId()}"],
                                $headline.$perex.$contents
                            )
                        );
            } else {
                $headline = Html::tag('div',
                            ['class'=>$this->classMap->getClass('Headline', 'div'),
                             'style' => "display: block;"
                            ],
                            $this->renderHeadline($paperAggregate)
                        );
                $perex = $this->renderPerex($paperAggregate);
                $contents = ($paperAggregate instanceof PaperAggregatePaperContentInterface) ? $this->renderContents($paperAggregate) : "";
                $html =
                    Html::tag('article', ['data-red-renderer'=>'PaperEditable'],
                            $headline.$perex.$contents
                    );
            }
        } else {
            $html = Html::tag('div', ['style'=>'visibility: hidden;'], 'No active paper.');
        }

        return $html ?? '';
    }
}