<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Article;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Red\Model\Entity\PaperInterface;

use Pes\Text\Html;
/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class SelectArticleTemplateRenderer extends HtmlRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var AuthoredViewModelInterface $viewModel */
        $contentId = $viewModel->getContentId();  // vrací ArticleInterface
        return Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/article/$contentId"],
                    Html::tag('div', ['id'=>"article_$contentId", 'class'=>'paper_template_select'], '')
                );
    }
}
