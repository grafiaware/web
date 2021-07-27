<?php
namespace Component\Renderer\Html\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

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
class PaperRenderer  extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paperAggregate = $viewModel->getPaper();  // vrací PaperAggregate
        $headline = $this->renderHeadline($paperAggregate);
        $perex = $this->renderPerex($paperAggregate);
        $contents = ($paperAggregate instanceof PaperAggregatePaperContentInterface) ? $this->renderContents($paperAggregate) : "";
        $html =
            Html::tag('article', ['data-red-renderer'=>'PaperRenderer'],
                    parent::render($viewModel)
            );
        return $html ?? '';
    }

    private function renderHeadline(PaperInterface $paper) {
        $headline = $paper->getHeadline();
        return
            Html::tag('div',
                        ['class'=>$this->classMap->getClass('Headline', 'div'),
                         'style' => "display: block;"
                        ],
                        Html::tag('headline',
                            ['class'=>$this->classMap->getClass('Headline', 'headline')],
                            $headline
                        )
                    );
    }

    private function renderPerex(PaperInterface $paper) {
        $perex = $paper->getPerex();
        return  $perex
                ?
                Html::tag('perex',
                    ['class'=>$this->classMap->getClass('Perex', 'perex')],
                    $perex
                )
                :
                ""
                ;
    }


    /**
     * Renderuje bloky s atributem id pro TinyMCE jméno proměnné ve formuláři
     *
     * @param MenuItemPaperAggregateInterface $paperAggregate
     * @param type $class
     * @return type
     */
    private function renderContents(PaperAggregatePaperContentInterface $paperAggregate) {
        $contents = $paperAggregate->getPaperContentsArraySorted(PaperAggregatePaperContentInterface::BY_PRIORITY);
        $innerHtml = [];
        foreach ($contents as $paperContent) {
            /** @var PaperContentInterface $paperContent */
            $innerHtml[] = $this->renderContent($paperContent);
        }
        return $innerHtml;
    }

    private function renderContent(PaperContentInterface $paperContent) {
        return  Html::tag('content', [
                            'id' => "content_{$paperContent->getId()}",
                            'class'=>$this->classMap->getClass('Content', 'content'),
                            'data-owner'=>$paperContent->getEditor()
                        ],
                    $paperContent->getContent()
                );
    }

}