<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Article;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Article\ArticleViewModelInterface;

use Red\Model\Entity\ArticleInterface;

use Pes\Text\Html;
/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class SelectArticleTemplateRenderer extends HtmlRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var ArticleViewModelInterface $viewModel */
        $article = $viewModel->getArticle();
        $contentId = $viewModel->getArticle()->getId();  // vrací ArticleInterface
        return
//        Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/article/$contentId"],
//                    Html::tag('div', ['id'=>"article_$contentId", 'class'=>'paper_template_select'], '')
//                )
                $this->renderSelectTemplate($article)
                .
                $viewModel->getArticle()->getContent();
    }

    private function renderSelectTemplate(ArticleInterface $article) {
        $contentTemplateName = $article->getTemplate();
        $paperId = $article->getId();
        return
        Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/template"],
            Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$paperId", "value"=>$contentTemplateName])
            .
            Html::tag('div', ['id'=>"paper_$paperId", 'class'=>'paper_template_select'],'')

        );
    }
}
