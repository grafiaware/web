<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\Renderer\Html\Content\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Red\Model\Entity\PaperSectionInterface;

use Pes\Text\Html;

/**
 * Description of ContentsRenderer
 *
 * @author pes2704
 */
class SectionsRenderer extends HtmlRendererAbstract {
    /**
     * Renderuje bloky s atributem id pro TinyMCE jméno proměnné ve formuláři
     *
     * @param MenuItemPaperAggregateInterface $paperAggregate
     * @param type $class
     * @return type
     */
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paperAggregate = $viewModel->getPaper();
        if ($paperAggregate instanceof PaperAggregatePaperContentInterface) {

            $contents = $paperAggregate->getPaperSectionsArraySorted(PaperAggregatePaperSectionInterface::BY_PRIORITY);
            $innerHtml = [];
            foreach ($contents as $paperContent) {
                /** @var PaperSectionInterface $paperContent */
                $innerHtml[] = $this->renderContent($paperContent);
            }
        } else {
            $innerHtml[] = '';
        }
        return $innerHtml;
    }

    private function renderContent(PaperSectionInterface $paperContent) {
        $html =
                Html::tag('section', ['class'=>$this->classMap->get('Content', 'section')],
                    Html::tag('content', [
                                'id' => "content_{$paperContent->getId()}",
                                'class'=>$this->classMap->get('Content', 'content'),
                                'data-owner'=>$paperContent->getEditor()
                            ],
                        $paperContent->getContent()
                    )
                );
        return $html;
    }
}
