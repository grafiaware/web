<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Model\Entity\PaperAggregateInterface;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Component\ViewModel\Authored\Paper\NamedPaperViewModelInterface;
use Pes\Text\Html;
/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class ArticleRenderer extends ArticleRendererAbstract {

    public function render($data=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $viewModel = $this->viewModel;
        $paperAggregate = $viewModel->getPaper();
        if (isset($paperAggregate)) {
            $html =
                Html::tag('article', ['data-renderer'=>'PaperEditable'],
                    Html::tag('div',
                        ['class'=>$this->classMap->getClass('Headline', 'div'),
                         'style' => "display: block;"
                        ],
                        $this->renderHeadline($paperAggregate)
                    ).
                    $this->renderPerex($paperAggregate).
                    $this->renderContents($paperAggregate)
                );
        } else {
            $html = Html::tag('div', ['style' => "display: none;"], 'No paper for rendering.');
        }
        return $html;
    }
}
