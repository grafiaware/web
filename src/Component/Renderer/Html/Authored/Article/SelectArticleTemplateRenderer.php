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
                $this->renderSelectTemplate($article)
                .
                $viewModel->getArticle()->getContent();
    }

    private function renderSelectTemplate(ArticleInterface $article) {
        $contentTemplateName = $article->getTemplate();
        $articleId = $article->getId();
        return
        Html::tag('div', [],
            Html::tag('button', [
                'class'=>$this->classMap->getClass('PaperTemplateSelect', 'div button'),
                'formtarget'=>'_self',
                'tabindex'=>'0'
                ],
                Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.hidden')], 'Šablony pro stránku')
                .Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.visible')],
                    Html::tag('i', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div i')])
                )
            )
            .
            Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.selectTemplate')],
                Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/$articleId/template"],
                    Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$articleId", "value"=>$contentTemplateName])
                    .
                    Html::tag('div', ['id'=>"paper_$articleId", 'class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.tinyArticleSelect')],'')
                )

            )
        );
    }
}
