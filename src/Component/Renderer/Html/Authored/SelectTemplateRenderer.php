<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Red\Model\Entity\ArticleInterface;

use Pes\Text\Html;
/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class SelectTemplateRenderer extends HtmlRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var AuthoredViewModelInterface $viewModel */
        $article = $viewModel->getArticle();
        $contentId = $viewModel->getArticle()->getId();  // vrací ArticleInterface
        return
                $this->renderSelectTemplate($article)
                .
                $viewModel->getArticle()->getContent()
                ;
    }

    private function renderSelectTemplate(ArticleInterface $article) {
        $contentTemplateName = $article->getTemplate();
        $articleId = $article->getId();
        return
        Html::tag('div', [],
            Html::tag('button', [
                'class'=>$this->classMap->get('PaperTemplateSelect', 'div button'),
                'formtarget'=>'_self',
                'tabindex'=>'0',
                'onclick'=>"togleTemplateSelect(event, 'select_template_article_$articleId'); "
                ],
                Html::tag('div', ['class'=>$this->classMap->get('PaperTemplateSelect', 'div.hidden')], 'Šablony pro stránku')
                .Html::tag('div', ['class'=>$this->classMap->get('PaperTemplateSelect', 'div.visible')],
                    Html::tag('i', ['class'=>$this->classMap->get('PaperTemplateSelect', 'div i')])
                )
            )
            .
            Html::tag('div', ['id'=>"select_template_article_$articleId",'class'=>$this->classMap->get('PaperTemplateSelect', 'div.selectTemplate')],
                Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/article/$articleId/template"],
//                    Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$articleId", "value"=>$contentTemplateName])
//                    .
                    Html::tag('div', ['id'=>"template_$articleId", 'class'=>$this->classMap->get('PaperTemplateSelect', 'div.tinySelectTemplateArticle')],'')
                )

            )
        );

        // kopie z PaperRendererEditable->renderSelectTemplate()

        return
            // id je parametr pro togleTemplateSelect(id) - voláno onclick na button 'Vybrat šablonu stránky'
            Html::tag('div', ['id'=>"select_template_paper_$paperId",'class'=>$this->classMap->get('PaperTemplateSelect', 'div.selectTemplate')],
                Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/template"],
                    [
                        Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$paperId", "value"=>$contentTemplateName]),
                        // class je třída pro selector v tinyInit var selectTemplateConfig
                        Html::tag('div', ['id'=>"paper_$paperId", 'class'=>$this->classMap->get('PaperTemplateSelect', 'div.tinySelectTemplatePaper')],''),
                    ]
                )
            );

    }
}
