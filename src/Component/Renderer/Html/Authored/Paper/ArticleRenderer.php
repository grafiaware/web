<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Paper;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Pes\Text\Html;
/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class ArticleRenderer extends ArticleRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paperAggregate = $viewModel->getPaper();  // vrací Paper nebo PaperAggregate
        if (isset($paperAggregate)) {
            $headline = Html::tag('div',
                        ['class'=>$this->classMap->getClass('Headline', 'div'),
                         'style' => "display: block;"
                        ],
                        $this->renderHeadline($paperAggregate)
                    );
            $perex = $this->renderPerex($paperAggregate);
            $contents = ($paperAggregate instanceof PaperAggregatePaperContentInterface) ? $this->renderContents($paperAggregate) : "";
            $html =
                Html::tag('article', ['data-renderer'=>'PaperEditable'],
                        $headline.$perex.$contents
                );
        } else {
            $html = Html::tag('div', ['style' => "display: none;"], 'No paper for rendering.');
        }
        return $html;
    }
}
